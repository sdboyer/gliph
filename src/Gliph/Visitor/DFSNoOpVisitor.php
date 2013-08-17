<?php

namespace Gliph\Visitor;

class DFSNoOpVisitor implements DFSVisitorInterface {
    public function onInitializeVertex($vertex, $source, \SplQueue $queue) {}
    public function onBackEdge($vertex, \Closure $visit) {}
    public function onStartVertex($vertex, \Closure $visit) {}
    public function onExamineEdge($from, $to, \Closure $visit) {}
    public function onFinishVertex($vertex, \Closure $visit) {}
}