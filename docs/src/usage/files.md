# Usage 

1. Open the Code Generator UI
- Visit the following route in your browser: /code-generator or your customized route path.

## Prefill Fields from SQL Table Query

This feature allows you to automatically populate model fields by parsing a SQL `CREATE TABLE` query.

-  🔧 How It Works

1. Paste a SQL `CREATE TABLE` statement into the provided textarea.
2. Click the **"Prefill"** button.
3. It auto-fills:
   - `model_name`: Converted to singular, PascalCase.
   - `fieldsData`: Populated with `column_name`, `data_type`, and default values.
4. Duplicate column names are ignored and reported.
5. A success message shows how many fields were added.

 ## Define Model & Fields

- Enter the Model Name

- Add fields with their: Name , Data type , Validation rules ,Foreign key options (for relations)

3. Define Relationships
Choose relationship type (e.g., hasOne, belongsTo, hasMany, belongsToMany)
Select target model and relationship keys.

4. Select Files , Methods and Traits you want to Generate.

5. Generate Files
 - Click "Generate" to generate the selected files.
- All files will be created in the paths defined in your config file, following Laravel conventions.