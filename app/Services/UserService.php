<?php

namespace App\Services;

use Storage;
use Image;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Http\UploadedFile;

/**
 * User Service.
 */
class UserService
{
    /**
     * The type of images for the user with their sizes.
     *
     * @var arry
     */
    public $imageSizes = [
        'original' => null,
        'medium' => ['width' => 640, 'height' => 640],
        'small' => ['width' => 320, 'height' => 320],
        'thumbnail' => ['width' => 160, 'height' => 160],
    ];

    /**
     * Construct.
     */
    public function __construct(User $user, UserMeta $user_meta)
    {
        $this->user = $user;
        $this->user_meta = $user_meta;
        $this->storage = Storage::disk();
    }

    /**
     * Get a user by the ID.
     *
     * @param int $id
     *
     * @return $user
     */
    public function get($id)
    {
        $user = $this->user->find($id);

        return ($user) ? $user : false;
    }

    /**
     * Create a user.
     *
     * @param object $request
     *
     * @return $user
     */
    public function create($input)
    {
        $user = $this->createUser($input);

        // TODO: Add name to databse if doesn't exist.

        // $this->welcomeUser($user);

        return $user;
    }

    /**
     * Create a user.
     *
     *  @param object $request
     */
    private function createUser($input)
    {
        $user = $this->user->fill($input);
        $user->password = bcrypt($user->password);
        $user->save();

        $this->createFirstList($user);
        $this->saveImage($user, $input);

        return $user;
    }

    /**
     * Update a user.
     *
     * @param int    $id
     * @param object $request
     *
     * @return $user
     */
    public function update($request, $id)
    {
        if (($user = $this->user->find($id)) &&
            (policy($this->user)->update($request->user(), $user))) {
            $user->fill($request->all())->save();

            $this->saveImage($user, $request->all(), $update = true);
            $this->updateMeta($user, $request->except(['profile_image']));

            return $user;
        }

        return false;
    }

    /**
     * Update user meta fields.
     *
     * @param object $user  User object
     * @param array  $inpit Input data
     */
    public function updateMeta($user, $input = [], $protected = false)
    {
        $meta_keys = $protected ? $this->user_meta->protectedKeys : $this->user_meta->keys;

        foreach ($meta_keys as $user_meta) {
            if ((array_has($input, $user_meta) && isset($input[$user_meta])) ||
                (isset($input[$user_meta]) and $input[$user_meta] == 0)) {
                $meta = $user->meta()->firstOrNew([
                    'user_id' => $user->id,
                    'meta_key' => $user_meta,
                ]);

                $meta->meta_value = $input[$user_meta];
                $meta->save();
            }
        }
    }

    /**
     * Location of saved images in storage.
     *
     * @var string
     */
    public function imagePath($user_id)
    {
        return "/public/images/user/$user_id";
    }

    /**
     * Delete User.
     *
     * @param object $request
     * @param int    $id
     *
     * @return bool
     */
    public function delete($request, $id)
    {
        if ($user = $this->user->find($id)) {
            if (policy($this->user)->update($request->user(), $user)) {
                return $user->delete();
            }
        }

        return false;
    }

    /**
     * Generate a username with the users first and last name.
     *
     * @param string $first_name
     * @param string $last_name
     *
     * @return string $usersname
     */
    private function generateUsername($first_name = ' ', $last_name = ' ')
    {
        $username_options = [
            $first_name.$last_name,
            $first_name[0].$last_name,
            $first_name[0].'_'.$last_name,
            $first_name.$last_name[0],
            $first_name.'_'.$last_name[0],
            $first_name[0].$last_name.rand(100, 999),
        ];

        foreach ($username_options as $username) {
            $username = trim(strtolower($username));

            if ($this->checkIfUsernameAvailable($username)) {
                return $username;
            }
        }
    }

    /**
     * Check if a username is available.
     *
     * @param string $username
     *
     * @return bool
     */
    private function checkIfUsernameAvailable($username)
    {
        return (boolean) !$this->user->where('username', $username)->first();
    }

    /**
     * Search for a user.
     *
     * @param object $request
     *
     * @return array
     */
    public function search($request)
    {
        $users = $this->user->where(function($query) use ($request) {
                    $query->where('username', 'like', "%$request->search%");
                })->paginate();
                
        // $users->each(function ($item) {
        //         $item->append(['is_connected']);
        // });

        return $users;
    }

    /**
     * Save the User image.
     *
     * @param array $input Input from request.
     */
    public function saveImage($user, $input, $update = false)
    {
        if (!empty($input['profile_image'])) {
            if ($update) {
                $this->removeImage($user);
            }

            $images = $this->createImages($user, $input);

            $this->updateMeta($user, ['profile_image' => $images]);
        }
    }

    /**
     * Create the User Images.
     *
     * @return The User Images
     */
    public function createImages($user, $input)
    {
        $file = $input['profile_image'];

        $image = \Image::make($file->getRealPath());

        $name = $file->hashName();

        $images = [];

        foreach ($this->imageSizes as $size => $value) {
            $image_name = $this->storeImage($user, $image, $name, $size);

            $images[$size] = $image_name;
        }

        return json_encode($images);
    }

    /**
     * Store user images.
     *
     * @param object $user
     * @param array  $image_size
     * @param string $name
     * @param array $size
     */
    private function storeImage($user, $image, $name, $size = null)
    {
        $path = "{$this->imagePath($user->id)}/{$size}";

        $image = $image->encode('jpg');

        $file = $this->storage->put("$path/$name", $image, ['public' => true]);

        return $name;
    }

    /**
     * Remove User Image.
     *
     * @param int $id The Id of the User
     */
    public function removeImage($user)
    {
        $profile_image = $user->meta()->first()->getValue('profile_image');

        if ($profile_image) {
            foreach ($this->imageSizes as $size => $value) {
                $this->storage->deleteDirectory($this->imagePath($user->id)."/$size");
            }
        }
    }
}
