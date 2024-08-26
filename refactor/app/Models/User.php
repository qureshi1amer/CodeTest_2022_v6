<?php

namespace DTApi\Models;
class User extends Authenticatable
{

    /**
     * Check if the user is a regular user.
     *
     * @return bool
     */
    public function isUser()
    {
        return $this->user_type == env('USER_ROLE_ID');
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->user_type == env('ADMIN_ROLE_ID');
    }

    /**
     * Check if the user is a super admin.
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->user_type == env('SUPERADMIN_ROLE_ID');
    }

    /**
     * Check if the user is either an admin or super admin.
     *
     * @return bool
     */
    public function isAdminOrSuperAdmin()
    {
        return $this->isAdmin() || $this->isSuperAdmin();
    }

    public  function getJobs()
    {
        if ($his->is('customer')) {
            return Job::forCustomer()->get();
        }

        if ($his->is('translator')) {
            return Job::newTranslatorJobs($his->id);
        }
        return collect(); // Return an empty collection if no jobs found
    }

    /* Note  All the foreign keys are assumed only */
    public function jobs()
    {
        return $this->hasMany(Job::class, 'user_id');
    }

    // If a user can have many jobs as a translator
    public function translatorJobs()
    {
        return $this->belongsToMany(Job::class, 'translator_job_rel', 'user_id', 'job_id');
    }
}
