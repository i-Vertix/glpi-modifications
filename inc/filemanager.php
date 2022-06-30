<?php

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

if (!function_exists("recurse_copy")) {
    function recurse_copy(string $src, string $dst)
    {
        $dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file !== '.') && ($file !== '..')) {
                if (is_dir($src . '/' . $file)) {
                    recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    if (is_file($dst . '/' . $file)) {
                        unlink($dst . '/' . $file);
                    }
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
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
    return is_file(GLPI_MOD_BACKUP_DIR . '/logos/logo-GLPI-100-black.png.orig');
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
    recurse_copy(GLPI_ROOT . '/pics/logos/', GLPI_MOD_BACKUP_DIR . '/logos/');
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
    return copy(GLPI_MOD_BACKUP_DIR . '/twig/page_card_notlogged.html.twig.orig', GLPI_ROOT . '/templates/layout/page_card_notlogged.html.twig');
}

function restoreHeadBackup(): bool
{
    if (!isHeadBackupPresent()) return false;
    unlink(GLPI_ROOT . '/templates/layout/parts/head.html.twig');
    return copy(GLPI_MOD_BACKUP_DIR . '/twig/head.html.twig.orig', GLPI_ROOT . '/templates/layout/parts/head.html.twig');
}

function restoreLogoBackup(): bool
{
    if (!isLogoBackupPresent()) return false;
    recurse_copy(GLPI_MOD_BACKUP_DIR . '/logos/', GLPI_ROOT . '/pics/logos/');
    return true;
}

function restoreFaviconBackup(): bool
{
    if (!isFaviconBackupPresent()) return false;
    unlink(GLPI_ROOT . '/pics/favicon.ico');
    return copy(GLPI_MOD_BACKUP_DIR . '/favicon.ico.orig', GLPI_ROOT . '/pics/favicon.ico');
}

function overwriteLoginPage(): bool
{
    if (!isLoginPageBackupPresent()) return false;
    unlink(GLPI_ROOT . '/templates/layout/page_card_notlogged.html.twig');
    return copy(GLPI_MOD_RESOURCE_DIR . '/twig/page_card_notlogged.html.twig.orig', GLPI_ROOT . '/templates/layout/page_card_notlogged.html.twig');
}

function overwriteHead(string $title = "GLPI"): bool
{
    if (!isHeadBackupPresent()) return false;
    unlink(GLPI_ROOT . '/templates/layout/parts/head.html.twig');
    if (!copy(GLPI_MOD_RESOURCE_DIR . '/twig/head.html.twig.orig', GLPI_ROOT . '/templates/layout/parts/head.html.twig')) {
        return false;
    }
    $content = file_get_contents(GLPI_ROOT . '/templates/layout/parts/head.html.twig');
    $content = str_replace("[[TITLE_PLACEHOLDER]]", $title, $content);
    return (bool)file_put_contents(GLPI_ROOT . '/templates/layout/parts/head.html.twig', $content);
}

function overwriteLogos(): bool
{
    if (!isLogoBackupPresent()) return false;

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
    if (!isFaviconBackupPresent()) return false;
    unlink(GLPI_ROOT . '/pics/favicon.ico');
    return copy(GLPI_MOD_RESOURCE_DIR . '/favicon.ico', GLPI_ROOT . '/pics/favicon.ico');
}




















