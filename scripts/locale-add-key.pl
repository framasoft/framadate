#!/usr/bin/perl
use strict;
use warnings;

use JSON;

my $json = JSON->new->utf8->space_before(0)->space_after(1)->indent(4)->canonical(1);

my $en_file = 'locale/en.json';
my $en;
{
    open my $fh, '<', $en_file or die;
    local $/ = undef;
    $en = <$fh>;
    close $fh;
}

$en = $json->decode($en);

my ($key, $trad) = $ARGV[0] =~ m#^([^:]*):(.*)$#;
$en->{$key}->{$trad} = $trad;

open my $fh, '>', $en_file or die;
print $fh $json->encode($en);
close $fh;
