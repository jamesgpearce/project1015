(function(){
    [SCRIPT];
    var r=[RESULT],
        o=[NESTED],
        d=[RELOAD]
        k='[COOKIE]',
        a='[ASSIGNOR]',
        u='[SEPARATOR]',
        v='[SUBSEPARATOR]',
        n=function(v){
            return [NORMALIZE];
        },
        c='',
        f,g;
    for(f in r){
        if(r[0]=='_'){continue;}
        var t=typeof r[f];
        if(t[0]=='f'){continue;}
        c+=(c?u:k+'=')+f+a;
        if(o&&t[0]=='o'){
            for(g in r[f]){
                c+=v+g+a+n(r[f][g]);
            }
        }else{
            c+=n(r[f]);
        }
    }
    c+=';path=/';
    try{
        document.cookie=c;
        if(d){document.location.reload();}
    }catch(e){}
})();
