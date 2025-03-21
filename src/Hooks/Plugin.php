<?php

namespace EpaycoSubscription\Woocommerce\Hooks;

class Plugin
{
    public const UPDATE_CREDENTIALS_ACTION = 'epaycosubscription_plugin_credentials_updated';

    public const UPDATE_STORE_INFO_ACTION = 'epaycosubscription_plugin_store_info_updated';

    public const UPDATE_TEST_MODE_ACTION = 'epaycosubscription_plugin_test_mode_updated';

    public const LOADED_PLUGIN_ACTION = 'epaycosubscription_main_plugin_loaded';

    public const ENABLE_CREDITS_ACTION = 'ep_enable_credits_action';

    public const EXECUTE_ACTIVATE_PLUGIN = 'ep_execute_activate';

    /**
     * Register to plugin update event
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function registerOnPluginCredentialsUpdate($callback): void
    {
        add_action(self::UPDATE_CREDENTIALS_ACTION, $callback);
    }

    /**
     * Register to plugin store info update event
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function registerOnPluginStoreInfoUpdate($callback): void
    {
        add_action(self::UPDATE_STORE_INFO_ACTION, $callback);
    }

    /**
     * Register to plugin test mode update event
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function registerOnPluginTestModeUpdate($callback): void
    {
        add_action(self::UPDATE_TEST_MODE_ACTION, $callback);
    }

    /**
     * Register to plugin loaded event
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function registerOnPluginLoaded($callback): void
    {
        add_action(self::LOADED_PLUGIN_ACTION, $callback);
    }

    /**
     * Register to credits activate event
     * @param mixed $callback
     *
     * @return void
     */
    public function registerEnableCreditsAction($callback)
    {
        add_action(self::ENABLE_CREDITS_ACTION, $callback);
    }

    /**
     * Execute credits activate event
     *
     * @return void
     */
    public function executeCreditsAction(): void
    {
        do_action(self::ENABLE_CREDITS_ACTION);
    }

    /**
     * Execute plugin loaded event
     *
     * @return void
     */
    public function executePluginLoadedAction(): void
    {
        do_action(self::LOADED_PLUGIN_ACTION);
    }

    /**
     * Execute credential update event
     *
     * @return void
     */
    public function executeUpdateCredentialAction(): void
    {
        do_action(self::UPDATE_CREDENTIALS_ACTION);
    }

    /**
     * Execute store info event
     *
     * @return void
     */
    public function executeUpdateStoreInfoAction(): void
    {
        do_action(self::UPDATE_STORE_INFO_ACTION);
    }

    /**
     * Execute test mode update event
     *
     * @return void
     */
    public function executeUpdateTestModeAction(): void
    {
        do_action(self::UPDATE_TEST_MODE_ACTION);
    }

    /**
     * Register activate event event
     * @param mixed $callback
     *
     * @return void
     */
    public function registerActivatePlugin($callback)
    {
        add_action(self::EXECUTE_ACTIVATE_PLUGIN, $callback);
    }

    /**
     * Execute plugin activate event
     *
     * @return void
     */
    public function executeActivatePluginAction(): void
    {
        do_action(self::EXECUTE_ACTIVATE_PLUGIN);
    }
}