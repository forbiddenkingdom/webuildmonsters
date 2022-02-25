<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class InstallerServiceProvider extends ServiceProvider
{

    /**
     * @var string
     */
    public static $lockCode = 'g9mZ)j5(GGGHsf';

    /*
     *
     */
    public static $acError = 'Failed to connect to License Server. Please try again or contact us if the error persists.';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Return lock code
     * @return mixed
     */
    public static function getLockCode(){
        return getLockCode();
    }

    /**
     * Returns list of script required extensions.
     *
     * @return array
     */
    public static function getRequiredExtensions()
    {
        return [
            'bcmath',
            'Ctype',
            'Fileinfo',
            'JSON',
            'Mbstring',
            'OpenSSL',
            'PDO',
            'Tokenizer',
            'XML',
            'cURL',
            'exif',
            'GD'
        ];
    }

    /**
     * Check if server passes the script requirements.
     *
     * @return bool
     */
    public static function passesRequirements()
    {
        $extensions = self::getRequiredExtensions();
        $passes = true;
        foreach ($extensions as $extension) {
            if (! extension_loaded($extension)) {
                $passes = false;
            }
        }
        if (! (version_compare(phpversion(), '7.2.5') >= 0)) {
            $passes = false;
        }

        return $passes;
    }

    /**
     * Checks if envato license key is valid.
     * @param string $code
     * @return bool
     */
    public static function getLicenseData($code = '')
    {
        if(!self::setLockCode()){
            return (object)['success' => false, 'error' => 'Lock code failed to set up.'];
        }
        try{
            $response = file_get_contents('https://license.qdev.tech/?code='.$code.'&activate=true');
            $response = json_decode($response);
            return $response;
        } catch (\Exception $exception) {
            return (object)['success' => false, 'error' => self::$acError];
        }
    }

    /**
     * Checks if script is already installed.
     * @return bool
     */
    public static function checkIfInstalled()
    {
        if (Storage::disk('local')->exists('installed')) {
            return true;
        }

        return false;
    }

    /**
     * Appends values to the env file during the installation.
     * @param $line
     */
    public static function appendToEnv($line)
    {
        file_put_contents(base_path().'/'.'.env', file_get_contents(base_path().'/'.'.env').$line."\r\n");
    }


    /**
     * Setting up the lock code
     * @return bool
     */
    public static function setLockCode(){
        return setLockCode(self::$lockCode);
    }
}
