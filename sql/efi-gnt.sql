CREATE TABLE gnn (
	gnn_id INT NOT NULL AUTO_INCREMENT,
	gnn_email VARCHAR(255),
	gnn_key VARCHAR(255),
	gnn_size INT,
	gnn_cooccurrence INT,
	gnn_filename VARCHAR(255),
	gnn_time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	gnn_time_started DATETIME,
	gnn_time_completed DATETIME,
	gnn_ssn_nodes INT,
	gnn_ssn_edges INT,
	gnn_gnn_pfams INT,
	gnn_gnn_nodes INT,
	gnn_gnn_edges INT,
	PRIMARY KEY(gnn_id)
);


ALTER TABLE gnn AUTO_INCREMENT = 1000;
