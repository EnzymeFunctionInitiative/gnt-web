<?php

abstract class DiagramJob {
    const Uploaded = "DIRECT";
    const UploadedZip = "DIRECT_ZIP";
    const BLAST = "BLAST";
    const IdLookup = "ID_LOOKUP";
    const FastaLookup = "FASTA";
    const UNKNOWN = "UNKNOWN";
    const GNN = "GNN";

    const JobCompleted = "job.completed";
    const JobError = "job.error";
}

?>

