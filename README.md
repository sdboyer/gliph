# Gliph

[![Build Status](https://travis-ci.org/sdboyer/gliph.png?branch=php53)](https://travis-ci.org/sdboyer/gliph)
[![Latest Stable Version](https://poser.pugx.org/sdboyer/gliph/v/stable.png)](https://packagist.org/packages/sdboyer/gliph)

Gliph is a **g**raph **li**brary for **PH**P. It provides graph building blocks and datastructures for use in other applications. It is designed with performance in mind, but primarily to provide a sane interface. Graphs are hard enough without an arcane API making it worse.

## TODOs

Lots. But, to start with:

- Port to, or provide a parallel implementation in, PHP 5.5. Generators and non-scalar keys from iterators make this all SO much better. In doing that, also shift as much over to traits as possible.
- Implement a generic breadth-first algorithm and its corresponding visitors.
- Implement a generic iterative deepening depth-first algorithm, and its corresponding visitors.
- Implement other popular connected components algorithms, as well as some shortest path algorithms (starting with Dijkstra)
- Write up some examples showing how to actually use the library.

## Acknowledgements

This library draws heavy inspiration from the [C++ Boost Graph Library](http://www.boost.org/libs/graph/doc).
