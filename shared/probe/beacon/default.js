(function(){
    [SCRIPT];
    var r=[RESULT],
        c='',
        o=[NESTED],
        k='[COOKIE]',
        u='[SEPARATOR]',
        v='[SUBSEPARATOR]',
        n=function(v){
            return [NORMALIZE];
        },
        f,g;
    for(f in r){
        if(r[0]=='_'){continue;}
        var t=typeof r[f];
        if(t[0]=='f'){continue;}
        c+=(c?u:k+'=')+f+':';
        if(o&&t[0]=='o'){
            for(g in r[f]){
                c+=v+g+':'+n(r[f][g]);
            }
        }else{
            c+=n(r[f]);
        }
    }
    c+=';path=/';
    try{
        document.cookie=c;
        document.location.reload();
    }catch(e){}
})();
