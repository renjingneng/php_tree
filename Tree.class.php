<?php

/**
 * 树类，适合少量数据，所有计算都在内存中完成
 */
class Tree
{
    private $originalList;
    private $refer;
    private $fieldKey;
    private $fields;
    private $pk;
    private $parentKey;
    private $childrenKey;
    private $sortKey;
    private $sortOrder;
    private $pathKey;
    private $sourceKey;

    /**
     * 参数说明
     * $pk:主键id的key名
     * $parentKey:父id的key名
     * $childrenKey:存放子类的key名
     * $sortKey:根据哪个key排序
     * $sortOrder:排序order  SORT_ASC|SORT_DESC
     * $pathKey:存放路径的key名
     * $sourceKey:哪个key当做路径
     */
    public function __construct($pk = "id", $parentKey = "pid", $childrenKey = "children", $sortKey = '', $sortOrder = SORT_ASC, $pathKey = '', $sourceKey = '')
    {
        $this->pk          = $pk;
        $this->parentKey   = $parentKey;
        $this->childrenKey = $childrenKey;
        $this->sortKey     = $sortKey;
        $this->sortOrder   = $sortOrder;
        $this->pathKey     = $pathKey;
        $this->sourceKey   = $sourceKey;
    }

    private function _sort(&$subtree)
    {
        foreach ($subtree as &$item) {
            if (isset($item[$this->childrenKey])) {
                $this->_sort($item[$this->childrenKey]);
            }
            $name[] = $item[$this->sortKey];
        }
        array_multisort($name, $this->sortOrder, SORT_REGULAR, $subtree);
    }

    private function _field(&$subtree)
    {
        foreach ($subtree as &$item) {
            if (isset($item[$this->childrenKey])) {
                $this->fields[] = $item[$this->fieldKey];
                $this->_field($item[$this->childrenKey]);
            } else {
                $this->fields[] = $item[$this->fieldKey];
            }
        }
    }

    private function _path(&$subtree)
    {
        foreach ($subtree as &$item) {
            if (isset($this->refer[$item[$this->parentKey]])) {
                $item[$this->pathKey] = array_merge($this->refer[$item[$this->parentKey]][$this->pathKey], [$item[$this->sourceKey]]);
            } else {
                $item[$this->pathKey] = [$item[$this->sourceKey]];
            }
            if (isset($item[$this->childrenKey])) {
                $this->_path($item[$this->childrenKey]);
            }
        }
    }

    private function _load()
    {
        //注意：不能用array_column($this->originalList, null, $this->pk)因为我们要的就是引用
        foreach ($this->originalList as $k => $v) {
            $this->refer[$v[$this->pk]] =& $this->originalList[$k];
        }
        //遍历1遍
        foreach ($this->originalList as $k => $v) {
            if (isset($this->refer[$v[$this->parentKey]])) {
                $parent                       =& $this->refer[$v[$this->parentKey]];//获取父分类的引用
                $parent[$this->childrenKey][] =& $this->originalList[$k];//在父分类的children中再添加一个引用成员
            }
        }
        $tree = $this->deepTree();
        if (!empty($this->pathKey)) {
            $this->_path($tree);
        }
        if (!empty($this->sortKey)) {
            $this->_sort($tree);
        }
    }

    public function load(&$data)
    {
        $this->originalList = $data;
        $this->refer        = null;
        $this->_load();
    }

    public function loadReference(&$data)
    {
        $this->originalList =& $data;
        $this->refer        = null;
        $this->_load();
    }

    /**
     * 生成树结构  ok
     */
    public function deepTree($root = 0)
    {
        $tree = array();
        foreach ($this->originalList as $k => $v) {
            if ($v[$this->parentKey] == $root) {
                $tree[] =& $this->originalList[$k];
            }
        }
        return $tree;
    }

    /**
     * 生成树结构-某树下的某字段以数组形式返回-未排重、未排序 ok
     */
    public function deepTreeField($fieldKey, $root = 0)
    {
        $subTree        = $this->deepTree($root);
        $this->fieldKey = $fieldKey;
        $this->fields   = [];
        $this->_field($subTree);
        return $this->fields;
    }

    /**
     * 获取所有父类id  ok
     */
    public function deepTreeParent($id)
    {
        $pids = [];
        while (1) {
            $pid = isset($this->refer[$id][$this->parentKey]) ? $this->refer[$id][$this->parentKey] : null;
            if (!$pid) {
                break;
            }
            array_unshift($pids, $pid);
            $id = $pid;
        }
        return $pids;
    }
}