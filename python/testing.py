import default_motif
from Bio import SeqIO
import os
import re

file_path = "/home/s2667265/public_html/uploads/glucose_6_phosphatase_Aves.fasta"

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
            print(f"Written {file_name}")
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
            
"""# Check for motif name
            if line.startswith("Motif ="):
                motif_name = line.split("=")[1].strip()
                motif_data["sequence_id"] = sequence_id
                motif_data["motif_name"] = motif_name
                continue  # Skip to the next line

            # Capture start position
            if "Start = position" in line:
                start_pos = int(line.split()[-2])
                motif_data["start"] = start_pos

            # Capture end position
            elif "End = position" in line:
                end_pos = int(line.split()[-2])
                motif_data["end"] = end_pos"""



# Query PROSITE for each sequence
basedir = "/home/s2667265/public_html/uploads/"
files = split_fasta(file_path, basedir)
motifs = "/home/s2667265/public_html/uploads/motifs"
for f in files:
    print(f)
    sequencef = os.path.join(basedir, f)
    motif_file = default_motif.query_prosite(sequencef, f)

    info = parse_result(motif_file)

    with open(motifs, "a") as target:
        target.write(info)

    # Delete the source file after appending
    os.remove(sequencef)
    os.remove(motif_file)


