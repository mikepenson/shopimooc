<?php
/**
 * Created by PhpStorm.
 * User: melonydi
 * Date: 2016/7/15
 * Time: 13:45
 */

/**
 * 检查分类下是否有产品
 * @param int $cid
 * @return array
 */
function checkProExist($cid){
    $sql="select * from imooc_pro where cId={$cid}";
    $rows=fetchAll($sql);
    return $rows;
}

/**
 * 添加商品
 * @return string
 */
function addPro(){
    $arr=$_POST;
    $arr['pubTime']=time();
    $path="./uploads";
    $uploadFiles=uploadFile($path);
    
    if(is_array($uploadFiles)&&$uploadFiles){
        foreach($uploadFiles as $key=>$uploadFile){
            thumb($path."/".$uploadFile['name'],"../image_50/".$uploadFile['name'],50,50);
            thumb($path."/".$uploadFile['name'],"../image_220/".$uploadFile['name'],220,220);
            thumb($path."/".$uploadFile['name'],"../image_350/".$uploadFile['name'],350,350);
            thumb($path."/".$uploadFile['name'],"../image_800/".$uploadFile['name'],800,800);
        }
    }
    $res=insert("imooc_pro",$arr);
    $pid=getInsertId();
    if($res&&$pid){
        foreach($uploadFiles as $uploadFile){
            $arr1['pid']=$pid;
            $arr1['albumPath']=$uploadFile['name'];
            addAlbum($arr1);
        }
        $mes="<p>添加成功!</p><a href='addPro.php' target='mainFrame'>继续添加</a>|<a href='listPro.php' target='mainFrame'>查看商品列表</a>";
    }else{
        foreach($uploadFiles as $uploadFile){
            if(file_exists("../image_800/".$uploadFile['name'])){
                unlink("../image_800/".$uploadFile['name']);
            }
            if(file_exists("../image_50/".$uploadFile['name'])){
                unlink("../image_50/".$uploadFile['name']);
            }
            if(file_exists("../image_220/".$uploadFile['name'])){
                unlink("../image_220/".$uploadFile['name']);
            }
            if(file_exists("../image_350/".$uploadFile['name'])){
                unlink("../image_350/".$uploadFile['name']);
            }
        }
        $mes="<p>添加失败!</p><a href='addPro.php' target='mainFrame'>重新添加</a>";

    }
    return $mes;
}

function editPro($id){
    $arr=$_POST;
    $path="./uploads";
    $uploadFiles=uploadFile($path);
    if(is_array($uploadFiles)&&$uploadFiles){
        foreach($uploadFiles as $key=>$uploadFile){
            thumb($path."/".$uploadFile['name'],"../image_50/".$uploadFile['name'],50,50);
            thumb($path."/".$uploadFile['name'],"../image_220/".$uploadFile['name'],220,220);
            thumb($path."/".$uploadFile['name'],"../image_350/".$uploadFile['name'],350,350);
            thumb($path."/".$uploadFile['name'],"../image_800/".$uploadFile['name'],800,800);
        }
    }
    $where="id=".$id;
    $res=update("imooc_pro",$arr,$where);
    $pid=$id;
    if($res&&$pid){
        if ($uploadFiles&&is_array($uploadFiles)){
        foreach($uploadFiles as $uploadFile){
            $arr1['pid']=$pid;
            $arr1['albumPath']=$uploadFile['name'];
            addAlbum($arr1);
        }
        }
        $mes="<p>编辑成功!</p><a href='listPro.php' target='mainFrame'>查看商品列表</a>";
    }else{
        if(is_array($uploadFiles)&&$uploadFiles) {
            foreach ($uploadFiles as $uploadFile) {
                if (file_exists("../image_800/" . $uploadFile['name'])) {
                    unlink("../image_800/" . $uploadFile['name']);
                }
                if (file_exists("../image_50/" . $uploadFile['name'])) {
                    unlink("../image_50/" . $uploadFile['name']);
                }
                if (file_exists("../image_220/" . $uploadFile['name'])) {
                    unlink("../image_220/" . $uploadFile['name']);
                }
                if (file_exists("../image_350/" . $uploadFile['name'])) {
                    unlink("../image_350/" . $uploadFile['name']);
                }
            }
        }
        $mes="<p>编辑失败!</p><a href='listPro.php' target='mainFrame'>重新编辑</a>";

    }
    return $mes;
}

function delPro($id){
    $where="id=".$id;
    $res=delete("imooc_pro",$where);
    $proImgs=getAllImgByProId($id);
    if ($proImgs&&is_array($proImgs)){
        foreach ($proImgs as $proImg){
            if (file_exists("uploads/".$proImg['albumPath']))
                unlink("uploads/".$proImg['albumPath']);
            if (file_exists("../image_50/".$proImg['albumPath']))
                unlink("../image_50/".$proImg['albumPath']);
            if (file_exists("../image_220/".$proImg['albumPath']))
                unlink("../image_220/".$proImg['albumPath']);
            if (file_exists("../image_350/".$proImg['albumPath']))
                unlink("../image_350/".$proImg['albumPath']);
            if (file_exists("../image_800/".$proImg['albumPath']))
                unlink("../image_800/".$proImg['albumPath']);
        }
    }
    $where="pid={$id}";
    $res1=delete("imooc_album",$where);
    if ($res&&res1){
        $mes="删除成功！<br/><a href='listPro.php' target='mainFrame'>查看商品列表</a>";
    }else{
        $mes="删除失败！<br/><a href='listPro.php' target='mainFrame'>重新删除</a>";
    }
    return $mes;
}

/**
 * 得到商品的所有信息
 * @return array
 */
function getAllProByAdmin(){
    $sql="select p.id,p.pName,p.pSn,p.pNum,p.mPrice,p.iPrice,p.pDesc,p.pubTime,p.isShow,p.isHot,c.cName from imooc_pro as p join imooc_cate as c on p.cId=c.id";
    $rows=fetchAll($sql);
    return $rows;
}

function getAllImgByProId($id){
    $sql="select albumPath from imooc_album where pid={$id}";
    $rows=fetchAll($sql);
    return $rows;
}

/**
 * 根据ID得到商品的详情信息
 * @param $id
 * @return array
 */
function getProById($id){
    $sql="select p.id,p.pName,p.pSn,p.pNum,p.mPrice,p.iPrice,p.pDesc,p.pubTime,
p.isShow,p.isHot,p.cId,c.cName from imooc_pro as p join imooc_cate as c on p.cId=c.id where p.id={$id}";
    $row=fetchOne($sql);
    return $row;
}

/**
 * 得到所有的商品
 * @return array
 */
function getAllPro(){
    $sql="select p.id,p.pName,p.pSn,p.pNum,p.mPrice,p.iPrice,p.pDesc,p.pubTime,p.isShow,p.isHot,c.cName from imooc_pro as p join imooc_cate as c on p.cId=c.id";
    $rows=fetchAll($sql);
    return $rows;
}

/**
 * 根据cId得到产品
 * @param $cId
 * @return array
 */
function getProsByCid($cId){
    $sql="select p.id,p.pName,p.pSn,p.pNum,p.mPrice,p.iPrice,p.pDesc,p.pubTime,p.isShow,
p.isHot,c.cName from imooc_pro as p join imooc_cate as c on p.cId=c.id where c.id={$cId} limit 4";
    $rows=fetchAll($sql);
    return $rows;
}

/**
 * 根据cId得到下4条产品
 * @param $cId
 * @return array
 */
function getSmallProsByCid($cId){
    $sql="select p.id,p.pName,p.pSn,p.pNum,p.mPrice,p.iPrice,p.pDesc,p.pubTime,p.isShow,
p.isHot,c.cName from imooc_pro as p join imooc_cate as c on p.cId=c.id where c.id={$cId} limit 4,4";
    $rows=fetchAll($sql);
    return $rows;
}

/**
 * 得到商品id和名称
 * @return array
 */
function getProInfo(){
    $sql="select id,pName from imooc_pro order by id asc";
    $rows=fetchAll($sql);
    return $rows;
}