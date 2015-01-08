var exec=require("child_process").exec,THREADS={MAX:parseInt(process.argv[3]) || 10,RUNNING:0},COUNT={FLIPKART:{PAGE:-9,INCR:10},PAYTM:{PAGE:0,INCR:1},SNAPDEAL:{PAGE:-50,INCR:50}}

function createThread(domain){
	if(THREADS.RUNNING<THREADS.MAX){
		THREADS.RUNNING++
		COUNT[domain].PAGE+=COUNT[domain].INCR
		console.log("\nNodeJS: Thread Started, PAGE = "+COUNT[domain].PAGE+"\n")
		exec("php "+domain.toLowerCase()+".php "+COUNT[domain].PAGE,function(e,o){
			console.log(o)
			THREADS.RUNNING--
			console.log("NodeJS: Thread Ended.")
			createThread(domain)
		})
		createThread(domain)
	}
}
if(process.argv[2]){
	var domain=process.argv[2].toUpperCase()
	if(domain in COUNT){
		createThread(domain)
	}
	else{
		console.log("Unsupported Domain : "+domain)
		process.exit()
	}
}
else{
	console.log("Syntax : nodejs index.js <Domain> [Speed:Optional ( N: INTEGER )]")
	process.exit()
}

