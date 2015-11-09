$|=1;		# Turn Autoflush on
$testPath=$ARGV[0];

open HEADERS, "gunzip -c $testPath/1_report.txt.gz |" or die $!;
my $req = 0;
my $reqHeader=0;
my $fullRequestHeaders="";
my %REQ_INFO;
my %AKAMAI_INFO;

while ($line=<HEADERS>) {
	chop($line);
	$line=~ s/\r//g;
	if ($line =~ /^Request details/) {
		$line=<HEADERS>;
		chop($line);
		$line=~ s/\r//g;
		if ($line =~ /^Request/) {
			@tmp = split / /, $line;
			$req = $tmp[1];
			$req =~ s/://;
			$AKAMAI_INFO{$req}{'Cacheable'} = "-";
			$AKAMAI_INFO{$req}{'X-Cache'} = "-";
			$AKAMAI_INFO{$req}{'X-Cache-Remote'} = "-";
			$AKAMAI_INFO{$req}{'CPCODE'} = "-";
			$AKAMAI_INFO{$req}{'TTL'} = "-";
			$AKAMAI_INFO{$req}{'EdgeIP'} = "-";
			$AKAMAI_INFO{$req}{'ParentIP'} = "-";
			$AKAMAI_INFO{$req}{'Last-Modified'} = "-";
      $AKAMAI_INFO{$req}{'Cache-Control'} = "-";
      $AKAMAI_INFO{$req}{'Date'} = "-";
      $AKAMAI_INFO{$req}{'Akamai-Cache-Key'} = "-";
      $REQ_INFO{$req}{'reqHeaderSize'} = "-";
      $REQ_INFO{$req}{'reqCookieSize'} = "-";
      $REQ_INFO{$req}{'setCookies'} = "-";
      $REQ_INFO{$req}{'setCookieSize'} = "-";
      $AKAMAI_INFO{$req}{'Varnish-X-Cahe'} = "-";
      $fullRequestHeaders="";
		}
	}
		
	
        if ($line =~ /^Varnish-X-Cache:/) {
                        @tmp = split / /, $line;
                        $AKAMAI_INFO{$req}{'Varnish-X-Cache'} = $tmp[1] if ($tmp[1] ne "");
        }

	if ($line =~ /^X-Check-Cacheable:/) {
			@tmp = split / /, $line;
			$AKAMAI_INFO{$req}{'Cacheable'} = $tmp[1] if ($tmp[1] ne "");
	}

	if ($line =~ /^X-Cache:/) {
			@tmp = split / /, $line;
			$AKAMAI_INFO{$req}{'X-Cache'} = $tmp[1] if ($tmp[1] ne "");
			$server=$tmp[3];
			$server=~ s/.deploy.akamaitechnologies.com//;
			$server=~ s/^a//;
			$server=~ s/-/./g;
			$AKAMAI_INFO{$req}{'EdgeIP'} = $server if ($server ne "");
	}

	if ($line =~ /^X-Cache-Remote:/) {
			@tmp = split / /, $line;
			$AKAMAI_INFO{$req}{'X-Cache-Remote'} = $tmp[1] if ($tmp[1] ne "");
      $server=$tmp[3];
      $server=~ s/.deploy.akamaitechnologies.com//;
      $server=~ s/^a//;
      $server=~ s/-/./g;
      $AKAMAI_INFO{$req}{'ParentIP'} = $server if ($server ne "");
	}	

	if ($line =~ /^X-Cache-Key:/) {
			@tmp = split / /, $line;
			@tmp = split /\//, $tmp[1];
						
			$AKAMAI_INFO{$req}{'CPCODE'} = $tmp[3] if ($tmp[3] ne "");
			$AKAMAI_INFO{$req}{'TTL'} = $tmp[4] if ($tmp[4] ne "");
			$line =~ s/X-Cache-Key: //;
			$AKAMAI_INFO{$req}{'Akamai-Cache-Key'} = $line;
	}	

        if ($line =~ /^Last-Modified:/) {
                        @tmp = split / /, $line;
                        $line =~ s/Last-Modified: //;
                        $AKAMAI_INFO{$req}{'Last-Modified'} = $line;
        }


        if ($line =~ /^Cache-Control:/) {
                        @tmp = split / /, $line;
                        $line =~ s/Cache-Control: //;
                        $AKAMAI_INFO{$req}{'Cache-COntrol'} = $line;
        }

        if ($line =~ /^Date:/) {
                        @tmp = split / /, $line;
                        $line =~ s/Date: //;
                        $AKAMAI_INFO{$req}{'Date'} = $line;
        }
}
close HEADERS;

open REQUESTS, "gunzip -c $testPath/1_IEWTR.txt.gz |" or die $!;
while ($line=<REQUESTS>) {
	chop($line);
	$line=~ s/\r//g;
	@tmp = split /\t/, $line;
	$req = $tmp[34];


	print "$line" . "\t" . 
				$AKAMAI_INFO{$req}{"CPCODE"} . "\t". 
				$AKAMAI_INFO{$req}{"TTL"} . "\t". 
				$AKAMAI_INFO{$req}{"Cacheable"} . "\t". 
				$AKAMAI_INFO{$req}{"X-Cache"} . "\t". 
				$AKAMAI_INFO{$req}{"X-Cache-Remote"} . "\t" .
        $AKAMAI_INFO{$req}{'EdgeIP'} . "\t" .
				$AKAMAI_INFO{$req}{'ParentIP'} 	 ."\t" .
				$AKAMAI_INFO{$req}{'Last-Modified'} . "\t" .
				$AKAMAI_INFO{$req}{'Cache-Control'} . "\t" .
				$AKAMAI_INFO{$req}{'Date'} . "\t" .
				$AKAMAI_INFO{$req}{'Akamai-Cache-Key'} . "\t" .
				$AKAMAI_INFO{$req}{'Varnish-X-Cache'} . "\n";
}
close REQUESTS;

