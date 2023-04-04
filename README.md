# -Une-Taupe-Chez-Vous_Symfony
Symfony / Rest API / Cors / Mailer
{<ul id="list-display">
        {% for item in form.list %}
            <li>
            {{ item }}
            </li>
        {% endfor %}
    </ul>
{% for address in form.list %}
    {{ form_row(address.content) }}
{% endfor %}
    <div>
        <span type="style" onclick="addToList()">Ajouter</span>
    </div> 
}