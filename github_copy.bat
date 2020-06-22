@echo off

rem 開発環境から、Github 同期ディレクトリへコピー

rem Model
xcopy C:\Example\connect-cms\app\ModelsOption "C:\Example\Github\connect-cms_option\app\ModelsOption" /d /s

rem Plugin
xcopy C:\Example\connect-cms\app\PluginsOption "C:\Example\Github\connect-cms_option\app\PluginsOption" /d /s

rem Databases
xcopy C:\Example\connect-cms\database\migrations_option "C:\Example\Github\connect-cms_option\database\migrations_option" /d /s

rem View
xcopy C:\Example\connect-cms\resources\views\plugins_option "C:\Example\Github\connect-cms_option\resources\views\plugins_option" /d /s
