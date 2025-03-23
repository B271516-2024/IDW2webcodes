import subprocess
import json
import sys
import align
import os
import uuid


def parse(mainreport):
    with open(mainreport, "r") as f:
        report = ""
        flag = False
        flag1 = False
        for line in f:
            if line.startswith("Type of analysis"):
                line = line.replace("\n", "<br>")
                report = report+ line
            if line.startswith("Model                  LogL"):
                line = line.replace("\n", "<br>")
                report = report+ line
                flag = True
            if flag == True and (not line.startswith("Model                  LogL")):
                line = line.replace("\n", "<br>")
                report = report+ line
                flag = False
            if line.startswith("CONSENSUS TREE"):
                line = line.replace("\n", "<br>")
                report = report+ line
                flag1 = True
            if line.startswith("TIME STAMP"):
                flag1 = False
            if flag1 == True and (not line.startswith("CONSENSUS TREE")):
                line = line.replace("\n", "<br>")
                report = report+ line
    return report

def produce_tree(file_path, basename,userid):
    unique = str(uuid.uuid4())[:8]
    basedir = "/home/s2667265/public_html/userdata/"+userid+"/iqtree" + unique + "/"
    subprocess.run(['mkdir', basedir], check=True)
    subprocess.run(['chmod', "777", basedir], check=True)
    out_align = basedir + basename+ ".aln"

    if align.align(file_path, out_align):
        command = [
            "/localdisk/home/ubuntu-software/iqtree-2.2.0-Linux/bin/iqtree",
            "-B", "1000",
            "-s", out_align,
            "--prefix", basename
        ]
        try:
            subprocess.run(command, stdout=subprocess.PIPE, stderr=subprocess.PIPE, cwd=basedir)
        except subprocess.CalledProcessError as e:
            print(f"Error while running iqtree: {e.stderr.decode()}")#: {e.stderr.decode()}
        
        os.remove(out_align)
    
    zipfile = f"/home/s2667265/public_html/userdata/{userid}/iqtree_{unique}_results.zip"
    command1 = f"cd {basedir} && zip -r {zipfile} ."
    with open(os.devnull, 'w') as devnull:
        subprocess.run(command1, shell=True, check=True, stdout=devnull, stderr=devnull)

    mainreport = basedir + basename + ".iqtree"
    report = parse(mainreport)
    tree_file = basedir + basename + ".treefile"

    tree_file =tree_file.replace("/localdisk/home/s2667265/public_html", "https://bioinfmsc8.bio.ed.ac.uk/~s2667265")
    zipfile =zipfile.replace("/localdisk/home/s2667265/public_html", "https://bioinfmsc8.bio.ed.ac.uk/~s2667265")

    response = {
        "mainreport": report,
        "tree_file": tree_file,
        "zipfile": zipfile
    }

    print(json.dumps(response))

if __name__ == "__main__":
    file_path = sys.argv[1]
    userid = sys.argv[3]
    basename = sys.argv[2]

    produce_tree(file_path, basename, userid)