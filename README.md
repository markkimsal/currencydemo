Currency Fair Demo
=====

The backend is built on 2 main components:
 
 * ZMWS Work Server
 * Ratchet WebSocket server

The 3rd technology is MetroPHP framework which is just there to quickly show a template page.  It plays an insignificant role in the demo.

The ZMWS worker source files are in _workers/_.  Each worker does one part of the proposed demo.

Submittx
-----
This worker accepts submitted transactions and passess them to the processor

Proctx
----
This worker compiles some stats from the stream of transactions (currently only counts total number of transactions)

Stattx
----
This worker displays the stats from Proctx to a Web page via Web Socket technology.  On an initial WS conneciton it will fill the client with currently saved stats.  During each message from Proctx it will update all clients with each individual transaction and a new number of total transactions.

Architecture
=====
The most challenging part was combining the Ratchet WebSocket server with the ZMWS workers.  In order to have a bridge between ZeroMQ and WS, the two components needed to agree upon using React's event loop.  This required some modifications to ZMWS and React's ZMQ component.  I found that the SocketWrapper in React didn't support the SNDMORE flag, which made it largely incompatible with most PHP-ZMQ demos or libraries.

This demo is setup for horizontal scaling with either a single master work server, or individual work servers and a master database of some kind (riak perhaps).


Performance
======
It's hard for me to test performance because my Internet connection is easily saturated.

On Using PHP
---
I thought about using NodeJS for this demo, but I felt more comfortable rapidly prototyping in PHP with a service that I wrote and undersand well (ZMWS).  When PHP7 comes out, it seems like the performance could double in real world scenarios, so the overall performance could be comparable with NodeJS.

Other opportunities for enhancing performance include compiling a libev or libevent extension for PHP and rewriting ZMWS to use more React components.  I've prototyped both solutions and they seem to offer a 5-10% performance improvement under contrived scenarios.
