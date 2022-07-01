<?php

/*
This file is part of the GLPI Modifications plugin.

The glpi-modifications plugin is free software;
you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
PGUM srl; either version 2 of the License, or
(at your option) any later version of the GNU General Public License.

The glpi-modifications plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with glpi-modifications. If not, see <http://www.gnu.org/licenses/>.
--------------------------------------------------------------------------
@package	glpi-modifications
@author		PGUM srl (https://github.com/i-Vertix/glpi-modifications)
@author		Stevenes Donato (https://github.com/stdonato/glpi-modifications)
@copyright	Copyright (c) 2022 PGUM srl
@license	GPLv3
            http://www.gnu.org/licenses/gpl.txt
@link		https://github.com/i-Vertix/glpi-modifications
@link		https://github.com/stdonato/glpi-modifications
@link		https://i-vertix.com
@link		http://www.glpi-project.org/
@since		2022
--------------------------------------------------------------------------
*/

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

Session::checkLoginUser();

if (!defined("GLPI_MOD_DIR")) {
    define("GLPI_MOD_DIR", GLPI_ROOT . "/plugins/mod");
}

if (!defined("GLPI_MOD_BACKUP_DIR")) {
    define("GLPI_MOD_BACKUP_DIR", GLPI_ROOT . "/plugins/mod/backups");
}

if (!defined("GLPI_MOD_RESOURCE_DIR")) {
    define("GLPI_MOD_RESOURCE_DIR", GLPI_ROOT . "/plugins/mod/resources");
}

if (!is_dir(GLPI_MOD_BACKUP_DIR) && !mkdir($concurrentDirectory = GLPI_MOD_BACKUP_DIR) && !is_dir($concurrentDirectory)) {
    die('Backup folder not created');
}

if (!is_dir(GLPI_MOD_BACKUP_DIR . '/twig') && !mkdir($concurrentDirectory = GLPI_MOD_BACKUP_DIR . '/twig') && !is_dir($concurrentDirectory)) {
    die('Backup folder not created');
}

if (!is_dir(GLPI_MOD_BACKUP_DIR . '/logos/') && !mkdir($concurrentDirectory = GLPI_MOD_BACKUP_DIR . '/logos/') && !is_dir($concurrentDirectory)) {
    die('Backup folder not created');
}

function isLoginPageBackupPresent(): bool
{
    return is_file(GLPI_MOD_BACKUP_DIR . '/twig/page_card_notlogged.html.twig.orig');
}

function isHeadBackupPresent(): bool
{
    return is_file(GLPI_MOD_BACKUP_DIR . '/twig/head.html.twig.orig');
}

function isLogoBackupPresent(): bool
{
    return is_file(GLPI_MOD_BACKUP_DIR . '/logos/logo-GLPI-100-black.png');
}

function isFaviconBackupPresent(): bool
{
    return is_file(GLPI_MOD_BACKUP_DIR . '/favicon.ico.orig');
}

function createLoginPageBackup(): bool
{
    if (isLoginPageBackupPresent()) return true;
    return copy(GLPI_ROOT . '/templates/layout/page_card_notlogged.html.twig', GLPI_MOD_BACKUP_DIR . '/twig/page_card_notlogged.html.twig.orig');
}

function createHeadBackup(): bool
{
    if (isHeadBackupPresent()) return true;
    return copy(GLPI_ROOT . '/templates/layout/parts/head.html.twig', GLPI_MOD_BACKUP_DIR . '/twig/head.html.twig.orig');
}

function createLogoBackup(): bool
{
    if (isLogoBackupPresent()) return true;

    copy(GLPI_ROOT . '/pics/logos/logo-G-100-black.png', GLPI_MOD_BACKUP_DIR . '/logos/logo-G-100-black.png');
    copy(GLPI_ROOT . '/pics/logos/logo-G-100-grey.png', GLPI_MOD_BACKUP_DIR . '/logos/logo-G-100-grey.png');
    copy(GLPI_ROOT . '/pics/logos/logo-G-100-white.png', GLPI_MOD_BACKUP_DIR . '/logos/logo-G-100-white.png');

    copy(GLPI_ROOT . '/pics/logos/logo-GLPI-100-black.png', GLPI_MOD_BACKUP_DIR . '/logos/logo-GLPI-100-black.png');
    copy(GLPI_ROOT . '/pics/logos/logo-GLPI-100-grey.png', GLPI_MOD_BACKUP_DIR . '/logos/logo-GLPI-100-grey.png');
    copy(GLPI_ROOT . '/pics/logos/logo-GLPI-100-white.png', GLPI_MOD_BACKUP_DIR . '/logos/logo-GLPI-100-white.png');

    copy(GLPI_ROOT . '/pics/logos/logo-GLPI-250-black.png', GLPI_MOD_BACKUP_DIR . '/logos/logo-GLPI-250-black.png');
    copy(GLPI_ROOT . '/pics/logos/logo-GLPI-250-grey.png', GLPI_MOD_BACKUP_DIR . '/logos/logo-GLPI-250-grey.png');
    copy(GLPI_ROOT . '/pics/logos/logo-GLPI-250-white.png', GLPI_MOD_BACKUP_DIR . '/logos/logo-GLPI-250-white.png');

    return true;
}

function createFaviconBackup(): bool
{
    if (isFaviconBackupPresent()) return true;
    return copy(GLPI_ROOT . '/pics/favicon.ico', GLPI_MOD_BACKUP_DIR . '/favicon.ico.orig');
}

function restoreLoginPage(): bool
{
    if (!isLoginPageBackupPresent()) return false;
    unlink(GLPI_ROOT . '/templates/layout/page_card_notlogged.html.twig');
    if (rename(GLPI_MOD_BACKUP_DIR . '/twig/page_card_notlogged.html.twig.orig', GLPI_ROOT . '/templates/layout/page_card_notlogged.html.twig')) {
        // DELETE TWIG CACHE
        $cacheDir = GLPI_ROOT . '/files/_cache/templates/06';
        if (is_dir($cacheDir)) {
            $dir = opendir($cacheDir);
            while (false !== ($file = readdir($dir))) {
                if (($file !== '.') && ($file !== '..') && str_ends_with($file, ".php")) {
                    unlink($cacheDir . '/' . $file);
                }
            }
            closedir($dir);
        }
        return true;
    }
    return false;
}

function restoreHeadBackup(): bool
{
    if (!isHeadBackupPresent()) return false;
    unlink(GLPI_ROOT . '/templates/layout/parts/head.html.twig');
    return rename(GLPI_MOD_BACKUP_DIR . '/twig/head.html.twig.orig', GLPI_ROOT . '/templates/layout/parts/head.html.twig');
}

function restoreLogoBackup(): bool
{
    if (!isLogoBackupPresent()) return false;
    rename(GLPI_MOD_BACKUP_DIR . '/logos/logo-G-100-black.png', GLPI_ROOT . '/pics/logos/logo-G-100-black.png');
    rename(GLPI_MOD_BACKUP_DIR . '/logos/logo-G-100-grey.png', GLPI_ROOT . '/pics/logos/logo-G-100-grey.png');
    rename(GLPI_MOD_BACKUP_DIR . '/logos/logo-G-100-white.png', GLPI_ROOT . '/pics/logos/logo-G-100-white.png');

    rename(GLPI_MOD_BACKUP_DIR . '/logos/logo-GLPI-100-black.png', GLPI_ROOT . '/pics/logos/logo-GLPI-100-black.png');
    rename(GLPI_MOD_BACKUP_DIR . '/logos/logo-GLPI-100-grey.png', GLPI_ROOT . '/pics/logos/logo-GLPI-100-grey.png');
    rename(GLPI_MOD_BACKUP_DIR . '/logos/logo-GLPI-100-white.png', GLPI_ROOT . '/pics/logos/logo-GLPI-100-white.png');

    rename(GLPI_MOD_BACKUP_DIR . '/logos/logo-GLPI-250-black.png', GLPI_ROOT . '/pics/logos/logo-GLPI-250-black.png');
    rename(GLPI_MOD_BACKUP_DIR . '/logos/logo-GLPI-250-grey.png', GLPI_ROOT . '/pics/logos/logo-GLPI-250-grey.png');
    rename(GLPI_MOD_BACKUP_DIR . '/logos/logo-GLPI-250-white.png', GLPI_ROOT . '/pics/logos/logo-GLPI-250-white.png');
    return true;
}

function restoreFaviconBackup(): bool
{
    if (!isFaviconBackupPresent()) return false;
    unlink(GLPI_ROOT . '/pics/favicon.ico');
    return rename(GLPI_MOD_BACKUP_DIR . '/favicon.ico.orig', GLPI_ROOT . '/pics/favicon.ico');
}

function overwriteLoginPage(): bool
{
    if (!isLoginPageBackupPresent()) {
        if (!createLoginPageBackup()) return false;
    }
    unlink(GLPI_ROOT . '/templates/layout/page_card_notlogged.html.twig');
    if (copy(GLPI_MOD_RESOURCE_DIR . '/twig/page_card_notlogged.html.twig.orig', GLPI_ROOT . '/templates/layout/page_card_notlogged.html.twig')) {
        // DELETE TWIG CACHE
        $cacheDir = GLPI_ROOT . '/files/_cache/templates/06';
        if (is_dir($cacheDir)) {
            $dir = opendir($cacheDir);
            while (false !== ($file = readdir($dir))) {
                if (($file !== '.') && ($file !== '..') && str_ends_with($file, ".php")) {
                    unlink($cacheDir . '/' . $file);
                }
            }
            closedir($dir);
        }
        return true;
    }
    return false;
}

function overwriteHead(string $title = "GLPI"): bool
{
    if (!isHeadBackupPresent()) {
        if (!createHeadBackup()) return false;
    }
    unlink(GLPI_ROOT . '/templates/layout/parts/head.html.twig');
    if (!copy(GLPI_MOD_RESOURCE_DIR . '/twig/head.html.twig.orig', GLPI_ROOT . '/templates/layout/parts/head.html.twig')) {
        return false;
    }
    $content = file_get_contents(GLPI_ROOT . '/templates/layout/parts/head.html.twig');
    $content = str_replace("[[TITLE_PLACEHOLDER]]", $title, $content);
    if (file_put_contents(GLPI_ROOT . '/templates/layout/parts/head.html.twig', $content)) {
        return true;
    }
    return false;
}

function overwriteLogos(): bool
{
    if (!isLogoBackupPresent()) {
        if (!createLogoBackup()) return false;
    }

    unlink(GLPI_ROOT . '/pics/logos/logo-G-100-black.png');
    unlink(GLPI_ROOT . '/pics/logos/logo-G-100-grey.png');
    unlink(GLPI_ROOT . '/pics/logos/logo-G-100-white.png');

    unlink(GLPI_ROOT . '/pics/logos/logo-GLPI-100-black.png');
    unlink(GLPI_ROOT . '/pics/logos/logo-GLPI-100-grey.png');
    unlink(GLPI_ROOT . '/pics/logos/logo-GLPI-100-white.png');

    unlink(GLPI_ROOT . '/pics/logos/logo-GLPI-250-black.png');
    unlink(GLPI_ROOT . '/pics/logos/logo-GLPI-250-grey.png');
    unlink(GLPI_ROOT . '/pics/logos/logo-GLPI-250-white.png');

    copy(GLPI_MOD_RESOURCE_DIR . '/logo-G-100.png', GLPI_ROOT . '/pics/logos/logo-G-100-black.png');
    copy(GLPI_MOD_RESOURCE_DIR . '/logo-G-100.png', GLPI_ROOT . '/pics/logos/logo-G-100-grey.png');
    copy(GLPI_MOD_RESOURCE_DIR . '/logo-G-100.png', GLPI_ROOT . '/pics/logos/logo-G-100-white.png');

    copy(GLPI_MOD_RESOURCE_DIR . '/logo-GLPI-100.png', GLPI_ROOT . '/pics/logos/logo-GLPI-100-black.png');
    copy(GLPI_MOD_RESOURCE_DIR . '/logo-GLPI-100.png', GLPI_ROOT . '/pics/logos/logo-GLPI-100-grey.png');
    copy(GLPI_MOD_RESOURCE_DIR . '/logo-GLPI-100.png', GLPI_ROOT . '/pics/logos/logo-GLPI-100-white.png');

    copy(GLPI_MOD_RESOURCE_DIR . '/logo-GLPI-250.png', GLPI_ROOT . '/pics/logos/logo-GLPI-250-black.png');
    copy(GLPI_MOD_RESOURCE_DIR . '/logo-GLPI-250.png', GLPI_ROOT . '/pics/logos/logo-GLPI-250-grey.png');
    copy(GLPI_MOD_RESOURCE_DIR . '/logo-GLPI-250.png', GLPI_ROOT . '/pics/logos/logo-GLPI-250-white.png');
    return true;
}

function overwriteFavicon(): bool
{
    if (!isFaviconBackupPresent()) {
        if (!createFaviconBackup()) return false;
    }
    unlink(GLPI_ROOT . '/pics/favicon.ico');
    return copy(GLPI_MOD_RESOURCE_DIR . '/favicon.ico', GLPI_ROOT . '/pics/favicon.ico');
}

function uploadImage(array $file, array $filetype, string $relativeOutput): bool
{
    if (isset($file["error"]) && $file["error"] !== 0) {
        return false;
    }

    $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
    if (!in_array($extension, $filetype, true)) return false;
    $output = GLPI_MOD_RESOURCE_DIR . '/' . $relativeOutput;
    return move_uploaded_file($file["tmp_name"], $output);
}



















