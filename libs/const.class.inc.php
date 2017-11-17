<?php

abstract class DiagramJob {
    const DIRECT = "DIRECT";
    const DIRECT_ZIP = "DIRECT_ZIP";
    const BLAST = "BLAST";
    const LOOKUP = "ID_LOOKUP";
    const FASTA = "FASTA";
    const UNKNOWN = "UNKNOWN";

    const JobCompleted = "job.completed";
}

?>

