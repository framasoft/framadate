#!/usr/bin/perl
use strict;
use warnings;

use JSON;

my $json = JSON->new->utf8->space_before(0)->space_after(1)->indent(4)->canonical(1);

my $en_file = 'po/default.json';
my $en;
{
    open my $fh, '<', $en_file or die;
    local $/ = undef;
    $en = <$fh>;
    close $fh;
}

$en = $json->decode($en);

my $new_json = {};
my $old_json = '';

while (defined(my $line = <STDIN>)) {
    $old_json .= $line;
}

$old_json = $json->decode($old_json);
for my $key (keys %{$old_json}) {
    my $index    = index($key, '.');
    my $real_key = substr($key, 0, $index++);
    my $trad_key = substr($key, $index);

    if ($old_json->{$key}) {
        $new_json->{$real_key}->{$trad_key} = $old_json->{$key};
    } else {
        $new_json->{$real_key}->{$trad_key} = $en->{$key};
    }
}

print $json->encode($new_json);
