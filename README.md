# GLPI Customization Plugin

## INFO

**GLPI version support starting from 10.0**

For older version support (< 10.0) head over to the original repository.

This repository is a fork
of [https://github.com/stdonato/glpi-modifications](https://github.com/stdonato/glpi-modifications). As the original
owner of this repository does not continue to support future versions of GLPI, we decided to maintain it.
We have rewritten most of the code but the functionalities stay more or less the same.

This plugin and it's maintainers are not connected to the GLPI project in any way.

## INSTALLATION

Download plugin archive from https://github.com/i-Vertix/glpi-modifications/releases for the required GLPI version and
unzip the archive to the glpi plugins folder.
After unzipping a new folder called "mod" should appear in your plugins folder. If not, make sure the unzipped folder is
located in the glpi plugins folder (glpi/plugins) and is renamed to "mod".

### :warning: Permissions :warning:

Please make sure the apache user has write access over the "mod" plugin directory and several files located in the GLPI
folder (list below).
This is needed because the plugin creates folders and files for backup and customization purposes.

:info: The backup directory is now located in `/files/_plugins/mod/backups`

List of GLPI files/folders where write access is needed:

* /files/_plugins
* /templates/layout/page_card_notlogged.html.twig
* /templates/layout/parts/head.html.twig
* /pics/logos/logo-G-100-black.png
* /pics/logos/logo-G-100-grey.png
* /pics/logos/logo-G-100-white.png
* /pics/logos/logo-GLPI-100-black.png
* /pics/logos/logo-GLPI-100-grey.png
* /pics/logos/logo-GLPI-100-white.png
* /pics/logos/logo-GLPI-250-black.png
* /pics/logos/logo-GLPI-250-grey.png
* /pics/logos/logo-GLPI-250-white.png
* /pics/favicon.ico

If the above steps are completed and permissions are granted, log in to your GLPI dashboard and install the GLPI
Modifications plugin.

## USAGE

To modify the login background image and icons head over to the plugin settings in the glpi plugin manager. From there
you can upload your resources. Please make sure to respect the required image resolutions to ensure proper
visualization.
You can also roll back all or single images if you'd like to. the plugin creates a backup for every replaced file.
If you uninstall the plugin or disable it, all backups are restored automatically.

> [!CAUTION]
> At the moment, the restore of backups on plugin deactivation/uninstall is not working.
> Related glpi issue: [https://github.com/glpi-project/glpi/issues/16075](https://github.com/glpi-project/glpi/issues/16075)

## UNINSTALL

Please uninstall the plugin before removing any plugin files! Otherwise, all backups of original files are lost. We do
not take any credit for deleted files!

## FEATURE LIST

- replace GLPI logo of login page and menu with custom logo
- add background to login page
