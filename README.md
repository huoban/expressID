自家用
日快递太多，录入签收快递用
只求实用性，啥装饰都没有。
日后也不准备在更新了，主要足够了。

搜索快递单号问题，数据库里ctrl+F把 
因为一年多时间里签收几百个快递，实际用到查询就1回。

savid_Class.php   快递公司名称类
-----以下三个版本功能都相同，却别只是css样式-----
saveid.php        SQLite3 数据库版 有点css样式，其他app调用
saveid-xcx.php    SQLite3 数据库版 小程序用 无css特效
saveid_txt.php    txt版 txt数据版 小程序用 无css特效

请求格式如
http://192.168.50.11/testget/saveid.php?key=aabb33&id=

key=aabb33  #密码
如果放在外网用，有略胜于无
id=  #后面是单号，小程序回自动添加。

![配套小程序](https://raw.githubusercontent.com/huoban/expressID/refs/heads/main/%E9%85%8D%E5%A5%97%E5%B0%8F%E7%A8%8B%E5%BA%8F.png)
