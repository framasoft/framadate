push-locales:
	zanata-cli -q -B push --errors --project-version `git branch | grep \* | cut -d ' ' -f2-`

pull-locales:
	zanata-cli -q -B pull --min-doc-percent 50 --project-version `git branch | grep \* | cut -d ' ' -f2-`

stats-locales:
	zanata-cli -q stats --project-version `git branch | grep \* | cut -d ' ' -f2-`
