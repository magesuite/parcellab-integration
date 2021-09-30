# Parcellab Integration

## How it works
Export happens automatically on creating new shipments and adding tracking numbers in the admin panel or via Magento API.

## Installation
``` shell
composer require creativestyle/parcellab-integration
```

## Managing Parcellab API Access
Login to https://prtl.parcellab.com/ with credentials provided by the merchant to get the credentials required by the magento module. 
Navigate to `Account -> Profile -> Manage API Access`

**Required steps to retrieve the credentials**

- Click on the button Generate new token
- Choose token type read/write
- Make up a name of the token to distinguish different tokens

## Module Configuration
Login to Magento Admin Panel and navigate to the module configuration page 
`Stores -> Configuration -> CreativeStyle -> Parcellab Integration`

Enable the module and copy/paste the credentials you got on the parcallab portal from the previous step.

| Config                                | Description                                                                                            |
|---------------------------------------|--------------------------------------------------------------------------------------------------------|
| `General/Enabled`                     | Module status                                                                                          |
| `General/Test Mode Enabled`           | If's enabled a custom field will be exported `"testShipment": true`                                    |
| `General/Api Url`                     | `https://api.parcellab.com/`                                                                           |
| `General/User ID`                     | User ID taken from parcellab portal                                                                    |
| `General/Token`                       | Token taken from parcellab portal                                                                      |
| `Auto Export/Enabled`                 | If enabled all created but not exported or failed shipments and trackings will be exported by schedule |
| `Auto Export/Cron Schedule`           | Cron schedule                                                                                          |
| `Auto Export/Allowed Order Statuses`  | By default all statuses are allowed                                                                    |

## Data Mapping

| Parcellab       | Magento                                                                                            |
|-----------------|--------------------------------------------------------------------------------------------------------|
| Tracking Number | Shipment **with tracking** number -> Tracking Number <br/> Shipment **without tracking** number -> Shipment Incremental ID 
| Order No.       | Shipment ID   
| Recipient       | Customer Details   
| Shipping        | Shipping Address   
| Articles        | Items Selected for the Shipment 
| Show all info   | Shipping Item Details
