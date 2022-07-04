# Read-only Field for Craft CMS

![Icon](resources/readonly.png)

A very simple read-only field type.

## Background

Sometimes you add content to Craft entries (for example via an API) that should be readable but not changeable for the user in the control panel.

## Requirements

* Craft CMS >= 4.0.0

## Installation

Open your terminal and go to your Craft project:

``` shell
cd /path/to/project
composer require codemonauts/craft-readonly-field
./craft plugin/install readonly
```

You can also install the plugin via the Plugin Store in the Craft Control Panel.

## Usage

After installation, the control panel can be used to create fields that behave like plaintext fields, but can only be filled programmatically with content. In the control panel, the content is displayed but cannot be edited.

With ❤ by [codemonauts](https://codemonauts.com)
