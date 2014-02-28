微信公众平台对接WordPress PHP SDK
=====

介绍
-----
  将微信公众平台完美的对接WordPress博客。


使用
-----

  1. Clone 或下载项目源码，上传至服务器。

  2. 进入[微信公众平台](https://mp.weixin.qq.com/)，高级功能，开启开发模式。
  
  2. 修改 `/src/menu.php` 中第 `76` 行中 `$menu_array` 的值为你的WordPress菜单布局对应的值，用浏览器打开 `/src/menu.php` 发送生成自定义菜单的请求，若果请求成功，重新关注以下你的微信公众号将能看到生成的菜单。
  
  3. 如果你使用自定义菜单按钮(以下简称 `菜单模式`)，请将 `/src/main.php` 第 `3~4` 行修改为
  ```php
  require("wechat.php");
  //require("wechat_normal.php");
  ```
  如果你不是用自定义菜单按钮(以下简称 `普通模式`)，请将 `/src/main.php` 第 `3~4` 行修改为
  ```php
  //require("wechat.php");
  require("wechat_normal.php");
  ```
  
  4. 去掉 `/src/main.php` 中第 `7` 行的注释，并注释掉第 `8` 行
  ```php
  $wechatObj->valid();
  //$wechatObj->responseMsg();
  ```

  5. `菜单模式` : 修改 `/src/wechat.php` 第 `2` 行，将 `shenyu` 替换成你设置的 `Token` 值;
  
  `普通模式` : 修改 `/src/wecha\_normal.php` 第 `2` 行，将 `shenyu` 替换成你想设置的 `Token` 值。


  6. 进入[微信公众平台](https://mp.weixin.qq.com/)，高级功能，设置接口配置信息。修改 `URL` 为 `/src/main.php` 的实际位置，修改 `Token` 为 `/src/wechat.php` 和 `src/wechat_normal.php` 中设置的 `Token` 值。

  4. 将 `/src/main.php` 中第 `7` 行注释掉，并去掉第 `8` 行的注释
  ```php
  //$wechatObj->valid();
  $wechatObj->responseMsg();
  ```

  6. `菜单模式` : 修改 `/src/wechat.php` 第 `6~9` 行，配置你的WordPress数据库相关信息；

  `普通模式` : 修改 `/src/wechat.php` 第 `6~9` 行，配置你的WordPress数据库相关信息。

  7. `普通模式` : 修改 `/src/wechat_normal.php` 第6行，配置你的WordPress地址，并且将 `104` 行 `$menuText` 修改成你的WordPress对应的布局。




