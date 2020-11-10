<?php

namespace MuCTS\Laravel\WeChatPayV3;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider implements DeferrableProvider
{
    /**
     * Boot the provider.
     */
    public function boot()
    {

    }

    /**
     * Register the provider.
     */
    public function register()
    {
        $this->setupConfig();

        $this->app->singleton("wechatpay-v3", function () {
            return new Factory();
        });

        $this->app->singleton('wechat.payment.v3', function () {
            $wechat = app('wechat.payment');
            Config::set('wechatpay-v3.app_id', $wechat->config['mch_id']);
            Config::set('wechatpay-v3.aes_key', $wechat->config['key']);
            Config::set('wechatpay-v3.serial_no', $wechat->config['serial_no']);
            Config::set('wechatpay-v3.private_key', file_get_contents($wechat->config['key_path']));
            return app('wechatpay-v3')::app();
        });
    }

    /**
     * Setup the config.
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__ . '/../config/wechatpay-v3.php');

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('wechatpay-v3.php')], 'wechatpay-v3');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('wechatpay-v3');
        }

        $this->mergeConfigFrom($source, 'wechatpay-v3');
    }
}
