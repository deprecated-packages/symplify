# How to Thank your Contributors

1. Setup Github config


```yaml
# statie.yml
parameters:
    thanker_repository_name: "tomasvotruba/tomasvotruba.cz"
    thanker_author_name: "TomasVotruba" # this name will be skipped from stats  
```

2. Dump contributors to `/source/_data/contributors.yml`

```bash
vendor/bin/statie dump-contributors
```

3. Use in *Thank you* template

```twig
{% block content %}
        <h2>Thanks to all <strong>{{ contributors|length }} contributors</strong> who help me create content!</h2>

        <p>
            {% set contributors = sort_by_field(contributors, 'name') %}
            
            {% for contributor in contributors %}
                {% if loop.last %}and{% endif %}
                <a href="https://github.com/{{ thanker_repository_name }}/commits?author={{ contributor.name }}">{{ contributor.name }}</a>{% if loop.index < (length(contributors) - 1) %}, {% endif %}{% if loop.last %}.{% endif %}

            {% endfor %}
        </p>
    </div>
{% endblock %}
``` 

Now you share gratitude for those, who help you on your life path. Good job!
