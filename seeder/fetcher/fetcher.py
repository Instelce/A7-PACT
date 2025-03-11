from selenium import webdriver
from selenium.webdriver.firefox.service import Service
from selenium.webdriver.common.by import By
from time import sleep
import json
import re

def main():
    file_name = "sorties-nature"
    url = 'https://www.cotesdarmor.com/agenda/sorties-nature/'

    service = Service('./geckodriver')
    driver = webdriver.Firefox(service=service)
    driver.get(url)

    sleep(2)

    counter_element = driver.find_element(By.CLASS_NAME, 'result-counter')
    counter = int(counter_element.text.split(' ')[0])
    plage_buttons = driver.find_elements(By.CLASS_NAME, 'list-item')
    first_button = plage_buttons[0]
    first_button.click()
    sleep(1)

    data = []

    # next_button = driver.find_element(By.CLASS_NAME, 'no-button')
    next_button = driver.find_element(By.XPATH, '/html/body/div[1]/section[2]/div/div/div/div/div/div/div[2]/div/div[2]/div[1]/div/div[3]/ul/li[3]/button')

    for i in range(counter):
        sleep(1)

        try:
            plage_data = {}

            data_container = driver.find_element(By.CLASS_NAME, 'diffusio-centerer')

            # Images
            try:
                images_container = data_container.find_element(By.CLASS_NAME, 'dsio-detail--photos')
                images = images_container.find_elements(By.TAG_NAME, 'img')
                plage_data['images'] = [img.get_attribute('src') for img in images]
            except:
                print('No images found')
                plage_data['images'] = []

            # Title
            title = data_container.find_element(By.TAG_NAME, 'h1')
            print(title.text)
            plage_data['title'] = title.text

            # Description
            try:
                description = data_container.find_element(By.XPATH, "/html/body/div[1]/section[2]/div/div/div/div/div/div/div[2]/div/div[2]/div[2]/div/div[1]/div/div[3]/div[2]/div[1]/p")
                print(description.text)
                plage_data['description'] = description.text
            except:
                print('No description found')
                plage_data['description'] = ''

            # Location
            try:
                location = data_container.find_element(By.XPATH, "/html/body/div[1]/section[2]/div/div/div/div/div/div/div[2]/div/div[2]/div[2]/div/div[1]/div/div[3]/div[2]/div[2]/div/div/p[1]/span[2]")
                print(location.text)
                plage_data['location'] = location.text
            except:
                print('No location found')
                plage_data['location'] = ''

            # Links
            sidebar = data_container.find_element(By.CLASS_NAME, 'dsio-detail--sidebar')
            sidebar_links = sidebar.find_elements(By.TAG_NAME, 'a')

            for link in sidebar_links:
                match = re.search(r'(-?\d+\.\d+),(-?\d+\.\d+)', link.get_attribute('href'))
#                 if link.text == 'Voir le numéro':
#                     print(link.get_attribute('href'))
#                     plage_data['phone'] = link.get_attribute('href')
                if link.text == 'E-mail':
                    print(link.get_attribute('href'))
                    plage_data['email'] = link.get_attribute('href')
                if link.text == 'Site internet':
                    print(link.get_attribute('href'))
                    plage_data['website'] = link.get_attribute('href')
                if match:
                    latitude, longitude = match.groups()
                    print(f'Latitude: {latitude}, Longitude: {longitude}')
                    plage_data['latitude'] = latitude
                    plage_data['longitude'] = longitude
                else:
                    print('No coordinates found')
                    plage_data['latitude'] = ''
                    plage_data['longitude'] = ''

            data.append(plage_data)

            # Save data to a file
            if i % 5 == 0:
                with open(f'data/{file_name}.json', 'w') as file:
                    file.write(json.dumps(data))
        except:
            print('No data found')

        next_button.click()

    # Save data to a file
    with open(f'data/{file_name}.json', 'w') as file:
        file.write(json.dumps(data))

    driver.quit()

if __name__ == '__main__':
    main()


