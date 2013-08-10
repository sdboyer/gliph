<?php

namespace Gliph\Traversal;

class DFSTopSort extends BFSTopSort {
    protected function nextVertex(\SplDoublyLinkedList $queue) {
        return $queue->pop();
    }
}