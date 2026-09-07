<?php

/**
 * UI Branding plugin for GLPI 12
 * Copyright (C) 2026 by i-Vertix/PGUM.
 * License GPLv3
 */

use GlpiPlugin\Mod\BrandManager;

function plugin_mod_install(array $params = []): bool
{
    try {
        (new BrandManager())->install();
        return true;
    } catch (\Throwable $e) {
        Session::addMessageAfterRedirect(
            sprintf(__('Unable to install UI Branding: %s', 'mod'), $e->getMessage()),
            true,
            ERROR
        );
        return false;
    }
}

function plugin_mod_uninstall(): bool
{
    try {
        (new BrandManager())->uninstall();
        return true;
    } catch (\Throwable $e) {
        Session::addMessageAfterRedirect(
            sprintf(__('Unable to uninstall UI Branding: %s', 'mod'), $e->getMessage()),
            true,
            ERROR
        );
        return false;
    }
}

function plugin_mod_activate(): bool
{
    try {
        $manager = new BrandManager();

        foreach (array_keys(BrandManager::getImageResources()) as $resource) {
            $manager->applyResource($resource);
        }

        $manager->applyLoginPageModifier();
        return true;
    } catch (\Throwable $e) {
        Session::addMessageAfterRedirect(
            sprintf(__('Unable to activate UI Branding: %s', 'mod'), $e->getMessage()),
            true,
            ERROR
        );
        return false;
    }
}

function plugin_mod_deactivate(): bool
{
    try {
        $manager = new BrandManager();

        foreach (array_keys(BrandManager::getImageResources()) as $resource) {
            $manager->restoreResource($resource);
        }

        $manager->disableLoginPageModifier();
        return true;
    } catch (\Throwable $e) {
        Session::addMessageAfterRedirect(
            sprintf(__('Unable to deactivate UI Branding: %s', 'mod'), $e->getMessage()),
            true,
            ERROR
        );
        return false;
    }
}
