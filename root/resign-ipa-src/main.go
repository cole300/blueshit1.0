package main

import (
	"bytes"
	"flag"
	"log"
	"net"
	"net/http"
	"os"
	"syscall"
	"time"
	//	"net/url"
	fcgi "bitbucket.org/PinIdea/fcgi_ext"
	gozd "bitbucket.org/PinIdea/zero-downtime-daemon"
    "fmt"
	"io"
	"os/exec"
	"path"
	"runtime"
	"runtime/debug"
	"strings"
)

const (
	HOMEDIR = "/Users/gwd/work"
)

type FastCGIServer struct{}

var (
	optCommand  = flag.String("s", "", "send signal to a master process: stop, quit, reopen, reload")
	optConfPath = flag.String("c", "", "set configuration file")
	optPidPath  = flag.String("pid", "", "set pid file")
	optHelp     = flag.Bool("h", false, "this help")
)

func usage() {
	log.Println("[command] -conf=[config file]")
	flag.PrintDefaults()
}

func (s FastCGIServer) ServeFCGI(resp http.ResponseWriter, req *http.Request, fcgi_params map[string]string) {
	defer func() {
		if r := recover(); r != nil {
			log.Println("Recovered in", r, ":")
			log.Println(string(debug.Stack()))
		}
	}()

	req.ParseMultipartForm(900000000)

	file, handler, err := req.FormFile("ipafile")
	if err != nil {
		log.Println("e1", req.MultipartForm)
		log.Println("e2", err)
		return
	}

	fname := strings.Replace(handler.Filename, " ", "_", -1)

	dirname := fname + "_" + (time.Now().Format("20060102150405"))
	dirname = strings.Replace(dirname, " ", "_", -1)
	dirname = strings.Replace(dirname, ".", "_", -1)
	dirname = strings.Replace(dirname, ":", "_", -1)
	dirname = strings.Replace(dirname, "+", "_", -1)

	fname = path.Join(os.TempDir(), fname)

	func() {
		defer file.Close()
		log.Println(handler.Header)

		f, err := os.OpenFile(fname, os.O_WRONLY|os.O_CREATE, 0666)
		if err != nil {
			log.Println("e3", err)
			return
		}
		defer f.Close()
		io.Copy(f, file)
	}()

	time.Sleep(1 * time.Second)
	
	home := path.Join(HOMEDIR, "/git/blueshit1.0/root/Resigned/", dirname)
	os.Mkdir(home, 0666)

	log.Println(HOMEDIR+"/git/blueshit1.0/root/ResignIPA.sh", fname, home)
	cmd := exec.Command(HOMEDIR+"/git/blueshit1.0/root/ResignIPA.sh", fname, home)

	out, err := cmd.CombinedOutput()
	if err != nil {
		log.Println("e4", err)
		return
	}

	log.Println(string(out))

	resp.WriteHeader(http.StatusOK)
	resp.Write([]byte("<html><head></head><body>"))
	
	out = bytes.Trim(out, "\n")
	arr := strings.Split(string(out), "\n")

	out = bytes.Replace(out, []byte("\n"), []byte("<br/>"), -1)

	resp.Write(out)

	if len(arr) > 0 && !strings.HasPrefix(arr[len(arr)-1], "ERROR") {

		resp.Write([]byte("<br/><a href=\"/?dir=" + "Resigned/" + dirname + "\">下载链接<a/><br/>"))

	}

	resp.Write([]byte("</body></html>"))

	os.RemoveAll(fname)
}

func handleListners(cl chan net.Listener) {
	for v := range cl {
		go func(l net.Listener) {
			srv := new(FastCGIServer)
			fcgi.Serve(l, srv)
		}(v)
	}
}

func main() {

	runtime.GOMAXPROCS(runtime.NumCPU()) //开启最大核心数 GO初始默认1个核心运行
	os.Setenv("GOTRACEBACK", "crash")

	// parse arguments after call flag.String() flag.Bool() flag.Int()
	flag.Parse()

	if *optHelp {
		usage()
		return
	}

	ctx := gozd.Context{
		Hash:    "go-resign-api-shooter-cn", //[DAEMON_NAME]
		Command: *optCommand, //[start,stop,reload]
		Maxfds:  syscall.Rlimit{Cur: 32677, Max: 32677}, //[RLIMIT_NOFILE_SOFTLIMIT,RLIMIT_NOFILE_HARDLIMIT]
		Pidfile: *optPidPath,
		Logfile: "go_resign_api_daemon.log",
		Directives: map[string]gozd.Server{
			"sock": gozd.Server{
				Network: "unix",
				Address: "/tmp/golang-resign.sock",
			},
		},
	}

	cl := make(chan net.Listener, 1)
	go handleListners(cl)
	sig, err := gozd.Daemonize(ctx, cl) // returns channel that connects with daemon
	if err != nil {
		log.Println("err: ", err)
		return
	}

	for s := range sig {
		switch s {
		case syscall.SIGTERM:
			// do some clean up and exit
			fmt.Println("doclean up")
			return
		}
	}
	fmt.Println("over")
}
