
  ��SAE���������ص���ǣ������С���ƽ̨���Ĺ��ܣ���������ͬ�ĳ�����룬 ������SAEƽ̨�����У���������ͨ���������С�

--------------------------------------------------------

  ��ƽ̨�ԣ���Ҫ������һ�¼������档

��1��ģ����뻺�档

  ����ͨ�����£����뻺�����������Runtime/CacheĿ¼�����ɡ�

  ��SAEƽ̨�£����뻺�����Memcache�洢�����뻺���ʱ����������һ�¡�������Memcache��Ӱ�졣

��2�����ݿ����á�

  ����ͨ�����£����ݿ�������config�ļ�����Ϊ׼��

  ��SAEƽ̨�£����ݿ�������̶�ΪSAE�ĳ������������˷ֲ�ʽ��������SEAƽ̨�£������޸����ݿ������

��3��ʹ��import������⣬��import("ORG.Net.UploadFile")

  ����ͨ�����£�����UploadFile.class.php�ļ���

  ��SAEƽ̨�£��������UploadFile_sae.class.php�ļ�������saeר����⣻��������ڣ�����������UploadFile.class.php�ļ�

��4��F��S���档

  ����ͨ�����£�Ĭ��Ϊ�ļ����淽ʽ��

  ��SAEƽ̨�£�Ĭ��ΪMemcache���淽ʽ��

��5��������ʹ��IS_SAE������
  
  ����ͨ�����£�IS_SAE��ֵΪfalse��

  ��SAEƽ̨�£�IS_SAE��ֵΪtrue��

------------------------------------------------------------

  ʹ�÷���:

  �˺���ֻ����չ��ThinkPHP��Mode����Ҫ���ļ�����ԭ���ĺ����ļ����У� ��SaeThinkPHP.php�ļ����ں�ԭ����ThinkPHP.php�ļ�ͬһ���ļ��С� ��Mode�ļ����µ��ļ�������ԭ�����ĵ�Mode�ļ����¡�

  Ȼ����Ҫ������ļ�����Sae�����ļ���

<?php
define("THINK_PATH","./ThinkPHP");
define("APP_NAME","App");
define("APP_PATH","./App");
require THINK_PATH."/SaeThinkPHP.php";//����Sae�����ļ�
App::run();
?>

  ��SAEƽ̨���ϴ��ļ���ֻ��Ҫ��UploadFile_sae.class.php�ļ����ں���ͨ�ϴ���UploadFile.class.php ͬһ���ļ��м��ɡ�

  �ϴ����벻�䡣 �磺

import("ORG.Net.UploadFile");
$upload=new UploadFile('', 'jpg,png,gif,bmp','','./Public/Upload/',"time");
if(!$upload->upload()){
            $this->error($upload->getErrorMsg());
}else{
            $info=$upload->getUploadFileInfo();
            dump($info['savename']);
}

  ����ͨ�����£��ᵼ��UploadFile.class.php�ļ����ϴ��ļ��ᱣ�浽./Public/Upload/Ŀ¼�¡�

  ��SAEƽ̨�£��ᵼ��UploadFile_sae.class.php�ļ����洢��domainΪUpload��storage�¡� 
SAEƽ̨�»���ϴ�Ŀ¼����storage��domain�� ���ϴ���./Public/Upload/�£�domain��ΪUpload�� �ϴ��� ./Public/Upload/img/�� domain��Ϊimg

