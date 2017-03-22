# magento2-richardma-export

## 安装
1. 将压缩包中的app和lib目录用cp命令复制到magento的安装目录下
1. 切换到magento根目录下
1. chown www:www ./ -R
1. php ./bin/magento setup:upgrade
1. php ./bin/magento cache:clean
1. chown www:www ./ -R

# 使用
1. 登陆magento后台
1. 在sales菜单中可以看到export addresslist和export order两个子菜单
1. 填入订单号码点击export按钮即可下载导出后的文件
1. 订单号码支持3-5的写法，表示3 4 5号订单，每个单独的订单号码使用英文逗号分割
