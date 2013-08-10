<?php

namespace Gliph\Traversal;

class DFSTopSort extends BFSTopSort {
    protected function queueProcessor(\SplDoublyLinkedList $queue) {
        return $queue->pop();
    }
}