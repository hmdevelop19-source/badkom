import re

with open('app/Models/Utd.php', 'r') as f:
    content = f.read()

# Check if mutasis relation already exists
if 'public function mutasis()' not in content:
    # Add mutasis relation before the closing brace
    relation = """
    public function mutasis()
    {
        return $this->hasMany(Mutasi::class, 'utd_id')->orderBy('id', 'desc');
    }
}"""
    content = content.replace('}', relation, 1)

with open('app/Models/Utd.php', 'w') as f:
    f.write(content)
