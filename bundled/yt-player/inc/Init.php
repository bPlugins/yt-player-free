<?php
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound
namespace YTP;


class Init{

    public static function get_services(){
        return [
            Page\Dashboard::class,
            PostType\YTPlayer::class,
            Base\Initialize::class,
            Page\Settings::class,
            Services\Shortcode::class,
            Services\CommonShortcode::class,
            Services\EnqueueAssets::class,
            Base\EnqueueAssets::class,
            Model\Ajax::class,
            Database\Init::class,
            Base\Presets::class,
        ];
    }

    private static $registered = false;

    public static function register_services(){
        if (self::$registered) {
            return;
        }
        self::$registered = true;

        foreach(self::get_services() as $class){
            $services = self::instantiate($class);
            if(method_exists($services, 'register')){
                $services->register();
            }
        }

    }

    private static function instantiate($class){
        if(class_exists($class)){
            return new $class();
        }
        
        return new \stdClass();
    }
}


