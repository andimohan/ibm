update general_journal_header set statuskey = 2 where statuskey = 3;
truncate chart_of_account_active_period;
delete from chart_of_account_amount;
insert into chart_of_account_active_period (`pkey`,`runningmonth`,`isclosed`) values (1,'2020-01-01',0);
delete from general_journal_header where annualclosingjournal = 1;
delete from general_journal_detail where refkey not in (select pkey from general_journal_header);
update  general_journal_detail,general_journal_header, chart_of_account set general_journal_header.statuskey = 4  where general_journal_header.pkey = general_journal_detail.refkey and general_journal_detail.coakey = chart_of_account.pkey and general_journal_header.statuskey <> 4 and
chart_of_account.isleaf = 0 and year(general_journal_header.trdate) < 2021;
update  general_journal_detail,general_journal_header set general_journal_header.statuskey = 4  where general_journal_header.pkey = general_journal_detail.refkey and general_journal_detail.coakey not in (select pkey from chart_of_account) and year(trdate) < 2021 ;

!-- update general_journal_header set statuskey = 4 where code in ('GJ05411','GJ05407','GJ05408','GJ05409','GJ05410','GJ30630');
