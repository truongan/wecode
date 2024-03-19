#!/bin/bash
python="python"
if [ "$EXT" = "py2" ]; then
	python="python2"
fi
if [ "$EXT" = "py3" ]; then
	python="python3"
fi

NEEDCOMPILE=1
if [ -f "$PROBLEMPATH/template.py" ]; then
	t="$PROBLEMPATH/template.py"
	f=$USERDIR/$FILENAME.$EXT
	banned=`sed -n -e '/###Begin banned keyword/,/###End banned keyword/p' $t | sed -e '1d' -e '$d'`
	code=`sed -e '1,/###End banned keyword/d' $t`
	while read -r line
	do
		if [[ "$line" == "" ]]; then
			continue
		fi
		line=`echo $line | tr -d '\r'`
		#echo grep -q "$line" $f
		if grep -q "$line" $f ;then
			echo "code.$EXT: forbidden phrase: \"$line\" is banned" >> cerr
			NEEDCOMPILE=0
		fi
	done <<< "$banned"
	echo "$code" | sed -e "/###INSERT CODE HERE/r $f" -e '/###INSERT CODE HERE/d' > $FILENAME.$EXT
else
	cp $USERDIR/$FILENAME.$EXT $FILENAME.$EXT
fi


if [ $NEEDCOMPILE -eq 0 ]; then
	EXITCODE=110
else

	cp $USERDIR/$FILENAME.$EXT $FILENAME.$EXT
	shj_log "Checking Python Syntax"
	# shj_log "$python -O -m py_compile $FILENAME.$EXT >/dev/null 2>cerr"

	shj_log "$tester_dir/run_judge_in_docker.sh "`pwd` "${languages_to_docker[$EXT]} $python -O -m py_compile $FILENAME.$EXT >/dev/null 2>cerr"
	$tester_dir/run_judge_in_docker.sh `pwd` ${languages_to_docker[$EXT]} $python -O -m py_compile $FILENAME.$EXT >/dev/null 2>cerr
	# $python -O -m py_compile $FILENAME.$EXT >/dev/null 2>cerr
	EXITCODE=$?
	COMPILE_END_TIME=$(($(date +%s%N)/1000000));
fi

shj_log "Syntax checked. Exit Code=$EXITCODE  Execution Time: $((COMPILE_END_TIME-COMPILE_BEGIN_TIME)) ms"
if [ $EXITCODE -ne 0 ]; then
	shj_log "Syntax Error"
	shj_log "$(cat cerr | head -10)"
	echo '<span class="text-primary">Syntax Error</span>' >$RESULTFILE
	echo '<span class="text-danger">' >> $RESULTFILE
	(cat cerr | head -10 | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $RESULTFILE
	echo "</span>" >> $RESULTFILE
	cd ..
	rm -r $JAIL >/dev/null 2>/dev/null
	shj_finish "Syntax Error"
fi

