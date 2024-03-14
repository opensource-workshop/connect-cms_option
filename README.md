# connect-cms_option
Connect-CMSのオプションプラグイン

「connect-cms_option」リポジトリは、Connect-CMS の標準パッケージには含まれない、オプションのプラグインなどを格納するためのリポジトリです。  
Connect-CMS の標準パッケージは以下を参照してください。  
https://github.com/opensource-workshop/connect-cms  
  
データベースの migration は以下のコマンドで行います。  
php artisan migrate --path=database/migrations_option  

## Dronstudy

Connect-CMS v1.8.0（予定）からBlocklyが本体に同梱されなくなりました。
DronStudyを利用する場合は、当リポジトリのblockly.zipを解凍して、Connect-CMSのpublic/jsディレクトリに追加してください。

##

<details>
<summary>dev_2_option_private.ps1.example</summary>

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
<summary>sync_option_private_2_dev.sh.example</summary>

```shell
# コピー元のルートPATH
$src_root_dir = "C:\path_to_connect-cms-option_dir\"
# コピー先のルートPATH
$dist_root_dir = "C:\path_to_dev_connect-cms\"

Copy-Item -Path "${src_root_dir}composer-option.json" -Destination "${dist_root_dir}"
Copy-Item -Path "${src_root_dir}composer-option.lock" -Destination "${dist_root_dir}"
```
</details>
