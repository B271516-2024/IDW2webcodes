import subprocess
import json
import sys
import os
from Bio import SeqIO
import uuid

# Function to query PROSITE with a single protein sequence
def query_prosite(sequence, i):
    with open(os.devnull, 'w') as devnull:
        outpath = "/home/s2667265/public_html/uploads/"+i +".motif"
        subprocess.run([
            "patmatmotifs",
            "-sequence", sequence,
            "-outfile", outpath
            ], stdout=devnull, stderr=devnull)
    return outpath


def split_fasta(input_fasta, basefir):
    # Open the input FASTA file
    with open(input_fasta, "r") as infile:
        # Iterate over each sequence in the FASTA file
        files = []
        for seq_record in SeqIO.parse(infile, "fasta"):
            # Create a separate file for each sequence
            file_name = f"{seq_record.id}.fasta"
            new_file = os.path.join(basefir,file_name)
            with open(new_file, "w") as out_file:
                SeqIO.write(seq_record, out_file, "fasta")
            files.append(file_name)
    return files


def parse_result(path):
    info=""
    first = True
    with open(path, "r") as content:
        motif_number=0
        for line in content:
            line = line.strip()
            # Check for the sequence ID
            if line.startswith("# Sequence:"):
                # Extract the sequence ID (e.g., "KAJ7421106.1")
                sequence_id = line.split()[2]
                if motif_number>0:
                    info = info+"Sequence: "+sequence_id+"\n"
            if line.startswith("# HitCount:"):
                motif_number = int(line.split()[-1])
            if motif_number > 0:
                if first == True:
                    info = info+"Sequence: "+sequence_id+"\n"
                    first = False
                if (not line) or line.startswith("#"):  # Skip comment lines
                    continue
                elif len(line.split())==2:
                    info = info+ "     " +line+"\n"
                    if all(part.isdigit() for part in line.split()):
                        motif_number=0
                else:
                    info = info+line+"\n"

    return info

def conclution(info):
    motif = []
    sequence = []
    for line in info:
        line = line.strip()
        if line.startswith("Motif") and line[8:] not in motif:
            motif.append(line[8:].strip())
        if line.startswith("Sequence"):
            sequence.append(line[10:].strip())

    m= ", ".join(motif)
    s=", ".join(sequence)

    report = f"<h4>Motif Analysis Completed</h4><br><ul>There are {len(motif)} different kinds of motif across the sequence(s) submited:<br>{m}<br>There are {len(sequence)} protein sequence(s) found to have one or more motifs:<br>{s}</ul><br><p>For more detailed infomation, please click on the download button</p>"
    return report

# Function to process multiple sequences and visualize motifs
def process_multiple_sequences(file_path, userid):
    unique = str(uuid.uuid4())[:8]
    basedir = f"/home/s2667265/public_html/userdata/{userid}/"
    files = split_fasta(file_path, basedir)
    motifs = f"/home/s2667265/public_html/userdata/{userid}/motifs{unique}"
    for f in files:
        sequencef = os.path.join(basedir, f)
        motif_file = query_prosite(sequencef, f)

        info = parse_result(motif_file)
        

        with open(motifs, "a") as target:
            target.write(info)

        # Delete the source file after appending
        os.remove(sequencef)
        os.remove(motif_file)

    with open(motifs, "r") as co:
        report = conclution(co)
    motifs = motifs.replace("/home/s2667265/public_html", "https://bioinfmsc8.bio.ed.ac.uk/~s2667265")
    data = {
        "file_path": motifs,
        "report": report
    }
    print(json.dumps(data))
    # Visualize motifs across sequences
    #visualize_motifs_across_sequences(sequences, motifs_per_sequence)
    

if __name__ == "__main__":
    file_path = sys.argv[1]
    userid = sys.argv[2]
# Run the analysis and visualization
    process_multiple_sequences(file_path, userid)
