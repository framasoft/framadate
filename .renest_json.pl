#!/usr/bin/perl
use strict;
use warnings;

use JSON;
#use Hash::Merge::Simple qw(merge);

my $json = JSON->new->utf8->space_before(0)->space_after(1)->indent(4)->canonical(1);

my $new_json = {};
my $old_json = '';

while (defined(my $line = <STDIN>)) {
    $old_json .= $line;
}

$old_json = decode_json($old_json);
for my $key (keys %{$old_json}) {
    $key =~ m/^([^.]*)\.(.*)$/;
    my $real_key = $1;
    my $trad_key = $2;

    $new_json->{$real_key}->{$trad_key} = $old_json->{$key} if $old_json->{$key};
}

print $json->encode($new_json);
