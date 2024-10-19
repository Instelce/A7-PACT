import json
from selenium import webdriver
from selenium.webdriver.chrome.service import Service as ChromeService
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.firefox.service import Service as FirefoxService
from webdriver_manager.firefox import GeckoDriverManager
from selenium.webdriver.common.by import By
from selenium.webdriver.support.wait import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
from datetime import datetime

entry = "https://www.tripadvisor.fr/Tourism-g2152091-Cotes_d_Armor_Brittany-Vacations.html"

# Set up the driver
options = Options()
# options.add_argument("--headless")
# driver = webdriver.Chrome(service=ChromeService(ChromeDriverManager().install()), options=options)
driver = webdriver.Firefox(service=FirefoxService(GeckoDriverManager().install()))

driver.get(entry)

# Get all activities
activities_link = []
activities_class = "DSinh"

# Wait until activities are loaded
WebDriverWait(driver, 10).until(
    EC.presence_of_element_located((By.CLASS_NAME, activities_class))
)

activities = driver.find_element(By.CLASS_NAME, activities_class).find_elements(By.TAG_NAME, "a")

for activity in activities:
    activities_link.append(activity.get_attribute("href"))

# Get all restaurants
restaurants_link = []
restaurants_class = "kEOWl"

# Wait until restaurants are loaded
WebDriverWait(driver, 10).until(
    EC.presence_of_element_located((By.CLASS_NAME, restaurants_class))
)

restaurants = driver.find_element(By.CLASS_NAME, restaurants_class).find_elements(By.TAG_NAME, "a")

for restaurant in restaurants:
    restaurants_link.append(restaurant.get_attribute("href"))

data = {
    "activities": activities_link,
    "restaurants": restaurants_link
}

# Save all activities
with open(f"../data/links-{datetime.now()}.json", "w") as f:
    json.dump(data, f)


driver.quit()