#!/bin/bash

# Set the environment variable
export OLLAMA_API_BASE=http://127.0.0.1:11434

# Run the Python script
python - <<EOF
import os
import litellm

# Environment variable is already set by the Bash script
response = litellm.completion(
    model='ollama3.1-8b', 
    provider='ollama', 
    prompt='Your prompt here'
)

print(response)
EOF