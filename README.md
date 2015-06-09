#EFI-GNT Web Interface

The EFI, http://enzymefunction.org,  has developed an easy to use program that enables a user to retrieve, display, and interact with genome neighborhood information for large datasets of pre-organized protein sequences. These sequence datasets are generally Pfam or InterPro families, or BLAST results. The web-based EFI-Genome Neighborhood Tool (GNT) is a companion to the EFI-Enzyme Similarity Tool (EST) and accepts a Sequence Similarity Network (SSN) as input. The program generates a Genome Neighborhood Network (GNN) file and a colored version of the input SSN. The process is extremely efficient and retrieves information (Pfam family, distance from query, etc.) for up to 20 “neighbor” protein sequences per query sequence and renders a network file, that can be imported and viewed in Cytoscape, in less than a minute for most SSN sizes. The only constraint applied to the retrieved neighborhood information is the distance from the query gene. Neighborhood information is then organized based on Pfam family classification thereby allowing one to establish functional relationships between Pfam families and SSN data.

Many existing genome neighborhood tools compare gene neighborhoods among multiple prokaryotic genomes in order to infer phylogenetic relationships. EFI-GNT is different in that it allows the comparison of genome neighborhoods between clusters of similar protein sequences in order to facilitate the assignment of function within protein families and superfamilies. 

This project is the web interface for the EFI-Genome Neighborhood Tool located at https://github.com/EnzymeFunctionInitiative/EST

## Installation
1.  Git Clone the repository
'''git clone https://github.com/EnzymeFunctionInitiative/gnt-web.git'''

2.Edit the php.ini file so it has the following settings.
        a. file_uploads = On
        b. upload_max_filesize = 2048M
        c. post_max_size = 2048M
        d. memory_limit = 4048M
        e. max_input_time = 100
        f. max_execution_time = 100

3.  Set permissions on /uploads and /html/output folder to allow apache user to read/write to it
4.  Add Alias in the apache config to point to the html folder
'''Alias /efi-gnt /var/www/efi-gnt/html'''
5.  Create Mysql database and user
6.  Import sql schema into mysql
'''mysql -u root -p efi-gnt < sql/efi-gnt.sql'''
7.  Copy conf/settings.inc.php.example to conf/settings.inc.php
8.  Edit conf/setting.inc.php with mysql information, location of gnn script, gnn module name
9.  Done
