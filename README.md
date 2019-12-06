# powerdns-to-bind
PHP script for export PTR and NS records from PowerDNS to Bind (KNOT) DNS zone files

## Configuration
Simply edit your PowerDNS database login for connection and save

## Run

```
$ php powerDnsToZones.php
```

Script will create temporary directory __tmp__ with zone files

### Example 
Sample zone file

```
$TTL    604800
$ORIGIN 0.168.192.in-addr.arpa. 
@    3600      SOA         ns1.domain.tld.  (
                           hostmaster.domain.tld. 
                           2019120601
                           1800
                           600
                           604800
                           600 )
                           86400      IN      NS      ns1.domain.tld.
                           86400      IN      NS      ns2.domain.tld.

0         IN      PTR      ptr-record-168-0.domain.tld.
1         IN      PTR      ptr-record-168-1.domain.tld.
2         IN      PTR      ptr-record-168-2.domain.tld.
3         IN      PTR      ptr-record-168-3.domain.tld.
```
