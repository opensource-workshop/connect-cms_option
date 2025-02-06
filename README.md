# connect-cms_option
Connect-CMSのオプションプラグイン

「connect-cms_option」リポジトリは、Connect-CMS の標準パッケージには含まれない、オプションのプラグインなどを格納するためのリポジトリです。  
Connect-CMS の標準パッケージは以下を参照してください。  
https://github.com/opensource-workshop/connect-cms  
  
データベースの migration は以下のコマンドで行います。  
```
php artisan migrate --path=database/migrations_option  
```

# オプションリポジトリ ←→ 開発環境にコピー

コピーシェル・バッチ等のサンプルです。<br>
環境に応じて修正して利用してください。<br>

<details>
<summary>(windows) dev_2_option_private.ps1.example</summary>

開発環境 → オプションリポジトリ にコピーするサンプル<br>
今のところ、composer-optionをコピーするのみ記載<br>

```shell
# コピー元のルートPATH
$src_root_dir = "C:\path_to_dev_connect-cms\"
# コピー先のルートPATH
$dist_root_dir = "C:\path_to_connect-cms-option_dir\"

### コピー（robocopy <コピー元> <コピー先>）
Copy-Item -Path "${src_root_dir}composer-option.json" -Destination "${dist_root_dir}"
Copy-Item -Path "${src_root_dir}composer-option.lock" -Destination "${dist_root_dir}"
```
</details>

<details>
<summary>(windows) option_private_2_dev.ps1.example</summary>

開発環境 → オプションリポジトリ にコピーするサンプル<br>
今のところ、composer-optionをコピーするのみ記載<br>

```shell
# コピー元のルートPATH
$src_root_dir = "C:\path_to_connect-cms-option_dir\"
# コピー先のルートPATH
$dist_root_dir = "C:\path_to_dev_connect-cms\"

Copy-Item -Path "${src_root_dir}composer-option.json" -Destination "${dist_root_dir}"
Copy-Item -Path "${src_root_dir}composer-option.lock" -Destination "${dist_root_dir}"
```
</details>

<details>
<summary>(windows) github_copy.bat</summary>

開発環境 → オプションリポジトリ にコピーするサンプル<br>
https://github.com/opensource-workshop/connect-cms_option/blob/master/github_copy.bat
</details>

<details>
<summary>(linux) sync_dev_2_option_private.sh.example</summary>

開発環境 → オプションリポジトリ にコピーするサンプル<br>

```shell
# Connect-CMSのあるディレクトリ
src_root_dir='/path_to_dev_connect-cms/'
# 外部プラグインのあるディレクトリ
dist_root_dir='/path_to_option_private_dir/'

####################################################
### connect-cms_option - Samplesプラグイン
####################################################
# プラグイン名
option_plugin="Samples"
option_plugin_controller_dir="${option_plugin}"
# ${変数,,}はbashの機能で、全小文字に変換する
option_plugin_resources_dir="${option_plugin,,}"
option_plugin_model_dir=$option_plugin_controller_dir

# コントローラー
rsync -arvz --delete "${src_root_dir}app/PluginsOption/User/${option_plugin_controller_dir}" "${dist_root_dir}app/PluginsOption/User/"

# モデル
rsync -arvz --delete "${src_root_dir}app/ModelsOption/User/${option_plugin_model_dir}" "${dist_root_dir}app/ModelsOption/User/"

# ビュー
rsync -arvz --delete "${src_root_dir}resources/views/plugins_option/user/${option_plugin_resources_dir}" "${dist_root_dir}resources/views/plugins_option/user/"

# マイグレーション
rsync -arvz --delete  --include '*sample*' --exclude 'migrations_option/*' "${src_root_dir}database/migrations_option" "${dist_root_dir}database/"

# Composer Option
cp -f "${src_root_dir}composer-option.json" "${dist_root_dir}"
cp -f "${src_root_dir}composer-option.lock" "${dist_root_dir}"
```
</details>

<details>
<summary>(linux) sync_option_private_2_dev.sh.example</summary>

オプションリポジトリ → 開発環境 にコピーするサンプル<br>

```shell
# 外部プラグインのあるディレクトリ
src_root_dir='/path_to_option_private_dir/'
# Connect-CMSのあるディレクトリ
dist_root_dir='/path_to_dev_connect-cms/'

####################################################
### connect-cms_option - Samplesプラグイン
####################################################
# プラグイン名
option_plugin="Samples"
option_plugin_controller_dir="${option_plugin}"
# ${変数,,}はbashの機能で、全小文字に変換する
option_plugin_resources_dir="${option_plugin,,}"
option_plugin_model_dir=$option_plugin_controller_dir

# コントローラー
rsync -arvz --delete "${src_root_dir}app/PluginsOption/User/${option_plugin_controller_dir}" "${dist_root_dir}app/PluginsOption/User/"

# モデル
if [ ! -d "${dist_root_dir}app/ModelsOption/User/" ]; then
    mkdir -p "${dist_root_dir}app/ModelsOption/User/"
fi
rsync -arvz --delete "${src_root_dir}app/ModelsOption/User/${option_plugin_model_dir}" "${dist_root_dir}app/ModelsOption/User/"

# ビュー
if [ ! -d "${dist_root_dir}resources/views/plugins_option/user/" ]; then
    mkdir -p "${dist_root_dir}resources/views/plugins_option/user/"
fi
rsync -arvz --delete "${src_root_dir}resources/views/plugins_option/user/${option_plugin_resources_dir}" "${dist_root_dir}resources/views/plugins_option/user/"

# マイグレーション
rsync -arvz --delete  --include '*sample*' --exclude 'migrations_option/*' "${src_root_dir}database/migrations_option" "${dist_root_dir}database/"

# Composer Option
cp -f "${src_root_dir}composer-option.json" "${dist_root_dir}"
cp -f "${src_root_dir}composer-option.lock" "${dist_root_dir}"
```
</details>
