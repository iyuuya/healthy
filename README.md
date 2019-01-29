# Healthy

Health check proxy web application

## Installation

```
git clone https://github.com/iyuuya/healthy.git
```

## Usage

```
bin/healthy --path /path/to/healthcheck --application wordpress-example --application drupal-example
```

## Configuration Apache2 Example

#### vhosts/default.conf
```
<VirtualHost _default_:80>
  ServerName healthy
  DocumentRoot "/path/to/healthcheck"
  <Directory /path/to/healthcheck>
    Options +Indexes +FollowSymLinks -MultiViews
    AllowOverride All
    Order allow,deny
    Allow from all
    Require all granted
  </Directory>
</VirtualHost>
```

#### vhosts/wordpress.conf

```
<VirtualHost *:80>
  ServerName wp.example.com
  # Specified the application name on healthy command
  ServerAlias wordpress-example
  DocumentRoot "/path/to/wordpress"
  ...
</VirtualHost>
```

### vhosts/drupal.conf
```
<VirtualHost *:80>
  ServerName dp.example.com
  # Specified the application name on healthy command
  ServerAlias drupal-example
  DocumentRoot "/path/to/druapl"
  ...
</VirtualHost>
```
