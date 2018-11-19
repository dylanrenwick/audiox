<?php

class UserApiController extends RestController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get()
    {
        // Get user ID from request
        $userId = Request::inUrl(0);
        // Error if no ID was given
        if (!$userId) return $this->error(4, 'You must provide a user ID!');

        // Fetch public profile of given ID
        $userProfile = UserModel::getPublicProfileOfUser($userId);
        // Error if no profile was found
        if (!$userProfile) return $this->error(5, 'No user with that ID exists!');

        // Return the public profile as JSON
        echo json_encode($userProfile);
    }
}