# mlmmj-light-web-ecg

## NOTE
This repository was forked from [GitHub](https://github.com/sergei-bondarenko/mlmmj-light-web) and is adapted to the needs of the ECG movement. All changes to the project will be tracked in this repository.

## Description

A light PHP web interface for managing [mlmmj](http://mlmmj.org/) mailing lists. It does not use a database and is available in English.


## Features

### For users
- Authentication via LDAP
- List all available mailinglists on the server
- Display owners and listdescription of the respective mailing lists on the index page
- Only show the edit function for mailing lists where the user is set as owner
- Edit functions per mailing list: subscribers, moderators, prefix and listdescription

### For admins
- Error handling regarding invalid user input
- Strip leading and trailing blanks for subscribers and moderators list
- Audit log of changes
- Notify admins about errors via Rocket:Chat bot implementation

---

## IMPORTANT
In case of login issues: Please be aware that the password input field gets sanitized using the filter [FILTER_SANITIZE_FULL_SPECIAL_CHARS](https://www.php.net/manual/en/filter.filters.sanitize.php)

---

## Installation

Clone the git repository to your webserver.```

Enter your ECG account credentials and the tool gets downloaded.

Change the values in `init.php` and you are ready to go:

| Variable | Description | Default value |
| --- | --- | --- |
| `$lists_path` | Path to the parent directory where the folder named at `$domain_global` is located at | `<Enter mlmmj directory>` |
| `$web_path` | Full path to the webinterface | `<Enter list.example.com/htdocs-ssl/mlmmj-light-web-ecg/ directory>` |
| `$web_url` | URL to the webinterface with leading https:// | `<Enter https://list.example.com/>`
| `$language` | As the original tool had two languages this is not relevant anymore. Currently the tool supports English only | `en` |
| `$domain_global` | Name of the folder within `$lists_path` where all mailing lists are stored | `mlmmj` |
| `$rc_webhook` | The Rocket:Chat webhook string to be able to send notifications regarding errors | Something like `ohciu8ni2aiBohciu/aikek3eikeche1eech8cumae3hiewuJ1ooqu0reik8feeGho` |
| `$current_version` | No need to change this | depends on the current version |
| `$headline` | No need to change this | `Manage your ECG mailing lists " . $current_version` |
| `$debug` | Turns the debug mode on which prints error messages on the screen | `false` |


### Restore original templates

If you want to restore the original templates:
1. Navigate to directory `misc`
2. Untar the following two archives:

```bash
tar xzvf ./templates.tar.gz -C .
tar xzvf ./smarty.tar.gz -C .
```

## Update

Enter the directory where the files are located.

```bash
cd mlmmj-light-web-ecg
git pull
```

Check if the values from `init.php` are still valid or need to be adapted.
