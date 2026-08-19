-- This script runs as the postgres superuser during container initialization.
-- It enables the pgvector extension so application migrations can use the
-- vector type without requiring superuser privileges.
CREATE EXTENSION IF NOT EXISTS vector;
