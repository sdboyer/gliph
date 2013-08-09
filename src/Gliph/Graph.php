<?php

namespace Gliph;

use Gliph\Util\HashMap;

class Graph {
    protected $v;

    public function __construct() {
        $this->v = new HashMap();
    }

    public function addVertex($v) {
        if (!isset($this->v[$v])) {
            $this->v[$v] = array();
        }
    }

    public function addDirectedEdge($u, $v) {
        $this->addVertex($u);
        $this->addVertex($v);
        $val = &$this->v->get($u);
        $val[] = $v;
    }

    public function removeVertex($v) {
        unset($this->v[$v]);
    }

    public function removeEdge($u, $v) {
        $val = &$this->v->get($u);
        unset($val[array_search($v, $val)]);
    }

    public function eachAdjacent($v, $callback) {
        foreach ($this->v[$v] as $e) {
            call_user_func($callback, $e);
        }
    }

    public function eachVertex($callback) {
        $this->fev(function ($v, $outgoing) use ($callback) {
            call_user_func($callback, $v);
        });
    }

    public function eachEdge($callback) {
        $edges = array();
        $this->fev(function ($v, $outgoing) use (&$edges) {
            foreach ($outgoing as $u) {
                $arr = new \SplFixedArray(2);
                $arr[0] = $v;
                $arr[1] = $u;
                $edges[] = $arr;
            }
        });

        foreach ($edges as $edge) {
            call_user_func($callback, $edge);
        }
    }

    public function hasVertex($v) {
        return isset($this->v[$v]);
    }

    protected function fev($callback) {
        for ($this->v->rewind(); $this->v->valid(); $this->v->next()) {
            $v = $this->v->key();
            $outgoing = $this->v->current();
            $callback($v, $outgoing);
        }
    }
}

