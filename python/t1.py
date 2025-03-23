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

print(parse("/home/s2667265/public_html/userdata/user1234/iqtreedc4f0f72/glucose_6_phosphatase_Aves.fasta.iqtree"))