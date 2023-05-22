#!/bin/bash
FECHA=$(date +%Y%m%d)
echo `date`
#cd /home/vguzman/routing
cd /home/aseopad/public_html/routing
#cd /var/www/html/pa/routing
wget -c http://download.geofabrik.de/south-america/colombia-latest.osm.bz2
echo `date`
bunzip2 colombia-latest.osm.bz2
echo `date`
#cd pgRouting-osm2pgrouting-fda9179
export PGPASSWORD=ohzsrj1
dropdb -U postgres routing
createdb -U postgres -O osm routing
createlang -U postgres plpgsql routing
psql -U postgres -d routing -f /usr/share/pgsql/contrib/postgis-64.sql
psql -U postgres -d routing -f /usr/share/pgsql/contrib/spatial_ref_sys.sql

psql -U postgres routing << EOF
ALTER TABLE geometry_columns OWNER TO osm;
ALTER TABLE geography_columns OWNER TO osm;
ALTER TABLE spatial_ref_sys OWNER TO osm;
EOF
psql -U postgres -f /usr/share/pgrouting/routing_core.sql routing
psql -U postgres -f /usr/share/pgrouting/routing_core_wrappers.sql routing
psql -U postgres -f /usr/share/pgrouting/routing_topology.sql routing
export PGPASSWORD=osm
/usr/local/src/osm2pgrouting/bin/osm2pgrouting -file colombia-latest.osm \
-conf mapconfig.xml \
-dbname routing \
-user osm \
-clean
echo `date`
#mv -v colombia-latest.osm /backup/$FECHA.colombia-latest.osm
rm -fv colombia-latest.osm
echo `date`

